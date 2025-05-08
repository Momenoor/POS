<?php

namespace App\Filament\Pages;

use App\Enums\BasicAccountsSetupEnum;
use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\Setup;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AccountsSetupPage extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $slug = 'accounts-setup';
    protected static ?string $navigationLabel = 'Accounts Setup';
    protected static string $view = 'filament.pages.accounts-setup';

    public ?array $data = [];
    protected array $accountsOptions = [];

    public function mount(): void
    {
        $this->accountsOptions = Account::query()
            ->whereIsLeaf()
            ->get()
            ->mapWithKeys(fn($account) => [
                $account->id => "[{$account->code}] - {$account->name}"
            ])
            ->toArray();

        $this->loadExistingSetup();
    }

    protected function loadExistingSetup(): void
    {
        $existingSetup = Setup::where('type', BasicAccountsSetupEnum::class)
            ->get()
            ->mapWithKeys(fn($setup) => [
                Str::lower($setup->name) => $setup->account_id
            ])
            ->toArray();

        $this->form->fill($existingSetup);
    }

    protected function getFormModel(): Model|string|null
    {
        return Setup::class;
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::FourExtraLarge;
    }

    public function getFormSchema(): array
    {

        return [
            Section::make('Basic Accounts Setup')
                ->description('Configure the default accounts for various transaction types')
                ->schema($this->getAccountSelectFields())
                ->columns(2)
                ->collapsible()
        ];
    }

    protected function getAccountSelectFields(): array
    {
        return collect(BasicAccountsSetupEnum::cases())
            ->flatMap(function ($case) {
                return [
                    Placeholder::make(Str::lower($case->name) . '__placeholder')
                        ->label($case->getLabel()),
                    Select::make(Str::lower($case->name))
                        ->label($case->getLabel())
                        ->required()
                        ->preload()
                        ->hiddenLabel()
                        ->options($this->accountsOptions)
                        ->searchable()
                ];
            })
            ->toArray();
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(fn() => $this->save())
                ->color('primary')
                ->keyBindings(['mod+s']),

            Action::make('reset')
                ->label(__('Reset'))
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset Form?')
                ->modalDescription('This will discard all changes and reload the original values')
                ->action(function () {
                    Notification::make()
                        ->title('Form reset')
                        ->success()
                        ->send();
                    $this->loadExistingSetup();
                }),

            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->color('gray')
                ->url(AccountResource::getUrl())
        ];
    }

    public function save(): void
    {
        try {
            DB::beginTransaction();

            $data = $this->form->getState();

            collect($data)->each(function ($accountId, $name) {
                Setup::updateOrCreate(
                    [
                        'name' => $name,
                        'type' => BasicAccountsSetupEnum::class
                    ],
                    ['account_id' => $accountId]
                );
            });

            DB::commit();

            Notification::make()
                ->title('Setup saved successfully')
                ->success()
                ->send();

            $this->loadExistingSetup();

        } catch (Halt $exception) {
            DB::rollBack();
            return;
        } catch (Throwable $exception) {
            DB::rollBack();

            Notification::make()
                ->title('Error saving setup')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            throw $exception;
        }
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }
}
