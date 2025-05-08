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

    protected static bool $isOpeningBalanceJournalEntry = false;

    public static function make(): static
    {
        return new static;
    }

    public function setAsOpeningBalanceJournalEntry(): static
    {
        self::$isOpeningBalanceJournalEntry = true;
        return $this;
    }

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

//    public static function updateAccountBalances(JournalEntry $journalEntry): void
//    {
//        // Process each journal item
//        foreach ($journalEntry->journalItems as $item) {
//            $account = $item->account;
//
//            // Skip if not a leaf account
//            if (!$account->isLeaf()) {
//                continue;
//            }
//
//            // Calculate amount change based on debit/credit
//            $amountChange = self::calculateAmountChange($item);
//
//            // Check if this is an opening balance entry
//            // Update the account's balances
//            if (self::$isOpeningBalanceJournalEntry) {
//                // For opening balance entries, update both opening and current balance
//                $account->update(['opening_balance'=> abs($amountChange)]);
//            }
//            $account->increment('current_balance', $amountChange);
//            $account->updateParentBalances($amountChange, true);
//
//        }
//    }
//
//    /**
//     * Reverse account balances when a journal entry is deleted
//     *
//     * @param \App\Models\JournalEntry $journalEntry
//     * @return void
//     */
//    public static function reverseAccountBalances(JournalEntry $journalEntry)
//    {
//        // Process each journal item
//        foreach ($journalEntry->journalItems as $item) {
//            $account = $item->account;
//
//            // Skip if not a leaf account
//            if (!$account->isLeaf()) {
//                continue;
//            }
//
//            // Calculate amount change (negative of the original)
//            $amountChange = -1 * self::calculateAmountChange($item);
//
//            // Check if this is an opening balance entry
//            // Reverse the account's balances
//            if (self::$isOpeningBalanceJournalEntry) {
//                // For opening balance entries, reverse both opening and current balance
//                $account->update(['opening_balance'=> abs($amountChange)]);
//            }
//            $account->decrement('current_balance', abs($amountChange));
//
//            $account->updateParentBalances($amountChange);
//            // Update all parent accounts based on an entry type
//
//            $account->updateParentBalances($amountChange, self::$isOpeningBalanceJournalEntry);
//
//        }
//    }
//
//    /**
//     * Calculate the net change in account balance from a journal item
//     *
//     * @param \App\Models\JournalItem $item
//     * @return float
//     */
//    protected static function calculateAmountChange(JournalItem $item): float
//    {
//        $account = $item->account;
//        $accountType = $account->type;
//
//        // Determine whether debits increase or decrease based on an account type
//        $isDebitIncreasing = in_array($accountType, ['asset', 'expense']);
//
//        // Calculate the net change
//        if ($isDebitIncreasing) {
//            return $item->debit - $item->credit;
//        } else {
//            return $item->credit - $item->debit;
//        }
//    }
//
//    public function updateParentBalances(float $amountChange, bool $isOpeningBalance = false): void
//    {
//        // Early return if there's no change or this isn't a leaf account
//        if ($amountChange == 0) {
//            return;
//        }
//
//        // Get all parent accounts in hierarchy
//        $parents = $this->getAllParents();
//
//        // Update each parent's current_balance
//        foreach ($parents as $parent) {
//            // Use DB::raw to ensure atomicity when updating
//            if($isOpeningBalance)
//            {
//                $parent->increment('opening_balance', $amountChange);
//            }
//            $parent->increment('current_balance', $amountChange);
//        }
//    }
//
//    public static function recalculateAllParentBalances(): void
//    {
//        // First, reset all parent account balances to opening_balance
//        self::parents()->update([
//            'current_balance' => DB::raw('opening_balance')
//        ]);
//
//        // Group all leaf account balances by parent_id
//        $leafAccounts = self::leafs()->get();
//
//        // Process each leaf account
//        foreach ($leafAccounts as $leafAccount) {
//            if ($leafAccount->parent_id) {
//                $leafAccount->updateParentBalances($leafAccount->current_balance - $leafAccount->opening_balance);
//            }
//        }
//    }
}
