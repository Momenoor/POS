<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Policies\AccountPolicy;
use App\Policies\BankAccountPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(BankAccount::class, BankAccountPolicy::class);
    }
}
