<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BasicAccountsSetupEnum: string implements HasLabel
{
    case ACCOUNTS_RECEIVABLE = 'Accounts Receivable';
    case ACCOUNTS_PAYABLE = 'Accounts Payable';
    case TAX = 'Tax';
    case CASH = 'Cash';
    case CREDIT_CARD = 'Credit Card';
    case BANK_TRANSFER = 'Bank Transfer';
    case CHEQUE = 'Cheque';
    case INVENTORY = 'Inventory';
    case DISCOUNT = 'Discount';
    case BANK_FEE = 'Bank Fee';
    case INTEREST = 'Interest';


    public function getLabel(): ?string
    {
        return $this->value;
    }
}
