<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Account,
    TaxRate,
    Restaurant,
    Staff,
    MenuCategory,
    MenuItem,
    Supplier,
    InventoryItem,
    InventoryTransaction,
    Table,
    Customer,
    Order,
    OrderItem,
    JournalEntry,
    JournalItem,
    Bill,
    Expense,
    Payment,
    BankAccount,
    BankReconciliation,
    BankTransaction,
    Shift,
    Setup,
    User
};
use App\Enums\{
    BankTransactionTypeEnum,
    InventoryTransactionTypeEnum,
    OrderStatusEnum,
    PaymentMethodEnum,
    TableStatusEnum
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AccountsSeeder::class,
        ]);
        User::create([
            'name' => 'Momen Noor',
            'email' => 'momen.noor@gmail.com',
            'password' => Hash::make('moLA@1324'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


}
