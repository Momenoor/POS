<?php

namespace App\Filament\Pages;

use App\Filament\Pages\OpeningBalanceSetupRelationManager\JournalEntryRelationManager;
use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\CalculationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;

class OpeningBalanceSetup extends Page implements HasForms
{
    use InteractsWithForms, InteractsWithFormActions, HasRelationManagers;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.opening-balance-setup';
    protected static ?string $navigationGroup = 'System Settings';
    public ?array $data = [];
    protected $accounts;

    public function mount(): void
    {
        $this->form->fill($this->loadDefaultData());

    }

    protected function getAccounts()
    {
        return $this->accounts ?? $this->accounts = Account::query()
            ->whereIsLeaf()
            ->get();
    }

    public function loadDefaultData(): array
    {
        $accounts = $this->getAccounts();
        return collect($accounts)->mapWithKeys(fn($account, $key) => [
            'data.account.' . $account->id . '.debit' => floatval($account->opening_balance) > 0 ? $account->opening_balance : 0,
            'data.account.' . $account->id . '.credit' => floatval($account->opening_balance) < 0 ? $account->opening_balance * -1 : 0,
            'data.account.' . $account->id . '.opening_balance' => floatval($account->opening_balance),
            'data.total_debit' => 0,
            'data.total_credit' => 0,
            'data.total_balance' => 0
        ])->toArray();
    }

    public function getFormSchema(): array
    {
        //dd($this->getSubFormSchema());
        return [
            Section::make('Opening Balance Setup')
                ->schema($this->getSubFormSchema())
                ->columns(4)
                ->collapsible()
        ];

    }

    public function getSubFormSchema(): array
    {
        $headers = [
            Placeholder::make('Account'),
            Placeholder::make('Debit'),
            Placeholder::make('Credit'),
            Placeholder::make('Balance'),
        ];
        $footers = [
            Placeholder::make('Total'),
            TextInput::make('data.total_debit')
                ->hiddenLabel()
                ->numeric()
                ->rules(['numeric', 'min:0'])
                ->default(0)
                ->currencyMask()
                ->readOnly()
                ->prefix('AED')
                ->reactive(),
            TextInput::make('data.total_credit')
                ->hiddenLabel()
                ->numeric()
                ->rules(['numeric', 'min:0'])
                ->default(0)
                ->currencyMask()
                ->readOnly()
                ->prefix('AED')
                ->reactive(),
            TextInput::make('data.total_balance')
                ->hiddenLabel()
                ->numeric()
                ->rules(['numeric', 'min:0'])
                ->default(0)
                ->currencyMask()
                ->readOnly()
                ->prefix('AED')
                ->reactive(),
        ];
        return array_merge($headers, collect($this->getAccounts())->flatMap(fn($account) => [
            PlaceHolder::make($account->name)
                ->content($account->formattedLabel)
                ->hiddenLabel(),
            TextInput::make("data.account.{$account->id}.debit")
                ->hiddenLabel()
                ->numeric()
                ->rules(['numeric', 'min:0'])
                ->default(0)
                ->currencyMask()
                ->prefix('AED')
                ->required()
                ->afterStateUpdated(function ($component, $state, Set $set, Get $get) {
                    $path = $component->getName(); // e.g., "account.1.debit"
                    $sibling = str_replace('debit', 'credit', $path);
                    if ($state > 0) {
                        $set($sibling, 0);
                    }
                    $balance = str_replace('debit', 'opening_balance', $path);
                    $set($balance, (($state) - $get($sibling)));
                    $this->calculateTotals();
                })
                ->lazy(),
            TextInput::make("data.account.{$account->id}.credit")
                ->hiddenLabel()
                ->numeric()
                ->rules(['numeric', 'min:0'])
                ->default(0)
                ->currencyMask()
                ->prefix('AED')
                ->required()
                ->afterStateUpdated(function ($component, $livewire, $state, Set $set, Get $get) {
                    $path = $component->getName(); // e.g., "account.1.debit"
                    $sibling = str_replace('credit', 'debit', $path);
                    if ($state > 0) {
                        $set($sibling, 0);
                    }
                    $balance = str_replace('credit', 'opening_balance', $path);
                    $set($balance, ($get($sibling) - $state));
                    $livewire->calculateTotals();
                })
                ->lazy(),
            TextInput::make('data.account.' . $account->id . '.opening_balance')
                ->hiddenLabel()
                ->currencyMask()
                ->prefix('AED')
                ->readOnly()
                ->reactive()
                ->required(),
        ])->toArray(),
            $footers);
    }

    public function calculateTotals()
    {
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;

        foreach ($this->getAccounts() as $account) {
            $debit = ($this->data['account'][$account->id]['debit'] ?? 0);
            $credit = ($this->data['account'][$account->id]['credit'] ?? 0);
            $balance = ($this->data['account'][$account->id]['opening_balance'] ?? 0);

            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance += $balance;
        }

        $this->data['total_debit'] = $totalDebit;
        $this->data['total_credit'] = $totalCredit;
        $this->data['total_balance'] = $totalBalance;
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(fn() => $this->save())
                ->color('primary')
                ->keyBindings(['mod+s']),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->color('gray')
                ->url(AccountResource::getUrl())
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::FiveExtraLarge;
    }


    public function save()
    {
        $formData = $this->form->getState();

        $oldJournalEntry = JournalEntry::where('memo', 'Auto Generated JV for Opening Balance Setup.')->first();

        if ($oldJournalEntry) {
            $oldJournalEntry->delete();
        }

        $journalEntryData = [
            'entry_date' => now(),
            'user_id' => auth()->id(),
            'reference_number' => 'JV-' . now()->format('Ymdhis') . '-' . $this->getAccounts()->count(),
            'memo' => 'Auto Generated JV for Opening Balance Setup.',
            'amount' => 0,
            'referenceable_id' => $this->getAccounts()->first()->id,
            'referenceable_type' => Account::class,
        ];


        $journalEntry = JournalEntry::query()->createQuietly($journalEntryData);
        Account::query()->update(['opening_balance' => 0]);
        // Process and save account opening balances
        foreach ($this->getAccounts() as $account) {
            $accountId = $account->id;
            $openingBalance = $formData['data']['account'][$accountId]['opening_balance'] ?? 0;

            // Update the account opening balance in the database
            $account->update([
                'opening_balance' => $openingBalance
            ]);
            if ($openingBalance !== 0) {
                $journalItemData = [
                    'account_id' => $accountId,
                    'debit' => $openingBalance > 0 ? $openingBalance : 0,
                    'credit' => $openingBalance < 0 ? $openingBalance * -1 : 0,
                    'memo' => 'Opening Balance for ' . $account->name,
                    'referenceable_id' => $journalEntry->id,
                    'referenceable_type' => JournalEntry::class,
                ];
                $journalItem = $journalEntry->journalItems()->create($journalItemData);
            }
        }
        Notification::make()->success('Opening balances saved successfully.');

        // Redirect to account list with success notification
        return redirect()->to(AccountResource::getUrl());
    }

    public function getAllRelationManagers(): array
    {
        return [
            JournalEntryRelationManager::class,
        ];
    }

    public function getRecord()
    {
        return $this->getAccounts()->first();
    }

    public function getRecordTitle(): string
    {
        return 'name';
    }

    public function getFormModel(): string|null
    {
        return Account::class;
    }
//    public function getFormStatePath(): ?string
//    {
//        return 'data';
//    }

}
