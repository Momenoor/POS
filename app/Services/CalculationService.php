<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculationService
{

    public static function calculateTransactionTotal(?bool $isNested = false, ?array $config = []): Closure
    {
        return function (Get $get, Set $set) use ($isNested, $config) {
            try {
                // Configuration with defaults
                $config = array_merge([
                    'expense_field' => 'expenses',
                    'expense_amount_field' => 'subtotal',
                    'bill_field' => 'bills',
                    'bill_amount_field' => 'total',
                    'output_field' => 'amount',
                    'precision' => 2,
                    'default_value' => 0,
                ], $config);

                // Determine field paths
                $pathPrefix = $isNested ? '../../' : '';
                $expensePath = $pathPrefix . $config['expense_field'];
                $billPath = $pathPrefix . $config['bill_field'];
                $amountPath = $pathPrefix . $config['output_field'];

                // Calculate totals
                $total = self::calculateExpensesTotal($get($expensePath) ?? [], $config)
                    + self::calculateBillsTotal($get($billPath) ?? [], $config);

                // Set the final amount
                $set($amountPath, round($total, $config['precision']));

            } catch (\Throwable $e) {
                Log::error("Bill total calculation failed: " . $e->getMessage(), [
                    'exception' => $e,
                    'config' => $config,
                    'isNested' => $isNested
                ]);
                $set($amountPath ?? 'amount', $config['default_value']);
            }
        };
    }

    protected static function calculateExpensesTotal(array $expenses, array $config): float
    {
        return array_reduce($expenses,
            fn($carry, $expense) => $carry + floatval($expense[$config['expense_amount_field']] ?? 0),
            0
        );
    }

    protected static function calculateBillsTotal(array $bills, array $config): float
    {
        return array_reduce($bills,
            fn($carry, $bill) => $carry + floatval($bill[$config['bill_amount_field']] ?? 0),
            0
        );
    }

    public static function updateBanksBalance(): void
    {
        $bankAccounts = BankAccount::all();
        $bankAccounts->each(function ($bankAccount) {
            $balance = $bankAccount->bankTransactions()
                ->select(DB::raw('SUM(CASE WHEN type = "deposit" THEN amount ELSE -amount END) as balance'))
                ->value('balance') ?? 0;

            $bankAccount->current_balance = $balance;
            $bankAccount->save();
        });
    }

    public static function updateAccountBalances(JournalEntry $journalEntry): void
    {

        // Process each journal item
        foreach ($journalEntry->items as $item) {
            $account = $item->account;

            // Skip if not a leaf account
            if (!$account->isLeaf()) {
                continue;
            }

            // Calculate amount change based on debit/credit
            $amountChange = self::calculateAmountChange($item);

            // Update the account's current balance
            $account->increment('current_balance', $amountChange);

            // Update all parent accounts
            $account->updateParentBalances($amountChange);
        }
    }

    /**
     * Reverse account balances when a journal entry is deleted
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return void
     */
    public static function reverseAccountBalances(JournalEntry $journalEntry)
    {
        // Process each journal item
        foreach ($journalEntry->items as $item) {
            $account = $item->account;

            // Skip if not a leaf account
            if (!$account->isLeaf()) {
                continue;
            }

            // Calculate amount change (negative of the original)
            $amountChange = -1 * self::calculateAmountChange($item);
            dd($amountChange);
            // Update the account's current balance
            $account->increment('current_balance', $amountChange);

            // Update all parent accounts
            $account->updateParentBalances($amountChange);
        }
    }

    /**
     * Calculate the net change in account balance from a journal item
     *
     * @param  \App\Models\JournalItem  $item
     * @return float
     */
    protected static function calculateAmountChange(JournalItem $item): float
    {
        $account = $item->account;
        $accountType = $account->type;

        // Determine whether debits increase or decrease based on an account type
        $isDebitIncreasing = in_array($accountType, ['asset', 'expense']);

        // Calculate the net change
        if ($isDebitIncreasing) {
            return $item->debit - $item->credit;
        } else {
            return $item->credit - $item->debit;
        }
    }
}
