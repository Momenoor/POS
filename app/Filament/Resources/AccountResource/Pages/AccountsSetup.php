<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\Page;

class AccountsSetup extends Page
{
    protected static string $resource = AccountResource::class;

    protected static string $view = 'filament.resources.account-resource.pages.accounts-setup';
}
