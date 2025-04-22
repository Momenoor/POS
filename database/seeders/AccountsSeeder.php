<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountsSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // Asset Accounts
            [
                'code' => '1000',
                'name' => 'Current Assets',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'All current assets'
            ],
            [
                'code' => '1100',
                'name' => 'Cash and Cash Equivalents',
                'type' => 'asset',
                'parent_account_id' => null, // Will update after parent is created
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Cash on hand and in bank'
            ],
            [
                'code' => '1110',
                'name' => 'Cash on Hand',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Physical cash in register'
            ],
            [
                'code' => '1120',
                'name' => 'Bank Accounts',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'All bank accounts'
            ],
            [
                'code' => '1121',
                'name' => 'Main Business Account',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Primary business checking account'
            ],
            [
                'code' => '1122',
                'name' => 'Savings Account',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Business savings account'
            ],
            [
                'code' => '1200',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Amounts owed by customers'
            ],
            [
                'code' => '1300',
                'name' => 'Inventory',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Food and beverage inventory'
            ],
            [
                'code' => '1400',
                'name' => 'Prepaid Expenses',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Prepaid rent, insurance, etc.'
            ],
            [
                'code' => '1500',
                'name' => 'Fixed Assets',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Long-term assets'
            ],
            [
                'code' => '1510',
                'name' => 'Equipment',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Kitchen equipment, furniture'
            ],
            [
                'code' => '1520',
                'name' => 'Accumulated Depreciation',
                'type' => 'asset',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Accumulated depreciation on fixed assets'
            ],

            // Liability Accounts
            [
                'code' => '2000',
                'name' => 'Current Liabilities',
                'type' => 'liability',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Short-term obligations'
            ],
            [
                'code' => '2100',
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Amounts owed to suppliers'
            ],
            [
                'code' => '2200',
                'name' => 'Accrued Expenses',
                'type' => 'liability',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Accrued wages, utilities, etc.'
            ],
            [
                'code' => '2300',
                'name' => 'Short-term Loans',
                'type' => 'liability',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Loans due within one year'
            ],
            [
                'code' => '2400',
                'name' => 'Sales Tax Payable',
                'type' => 'liability',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Collected sales tax not yet remitted'
            ],

            // Equity Accounts
            [
                'code' => '3000',
                'name' => 'Owner\'s Equity',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Partners\' capital accounts'
            ],
            [
                'code' => '3100',
                'name' => 'Partner 1 Capital',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Capital account for Partner 1'
            ],
            [
                'code' => '3200',
                'name' => 'Partner 2 Capital',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Capital account for Partner 2'
            ],
            [
                'code' => '3300',
                'name' => 'Partner 3 Capital',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Capital account for Partner 3'
            ],
            [
                'code' => '3400',
                'name' => 'Partner 4 Capital',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Capital account for Partner 4'
            ],
            [
                'code' => '3500',
                'name' => 'Partner 5 Capital',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Capital account for Partner 5'
            ],
            [
                'code' => '3600',
                'name' => 'Retained Earnings',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Accumulated profits/losses'
            ],
            [
                'code' => '3700',
                'name' => 'Prior Year Losses',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Accumulated losses from previous years'
            ],
            [
                'code' => '3800',
                'name' => 'Drawings',
                'type' => 'equity',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Owner withdrawals'
            ],

            // Revenue Accounts
            [
                'code' => '4000',
                'name' => 'Sales Revenue',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Income from food sales'
            ],
            [
                'code' => '4100',
                'name' => 'Sandwich Sales',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Revenue from sandwich sales'
            ],
            [
                'code' => '4200',
                'name' => 'Drink Sales',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Revenue from beverage sales'
            ],
            [
                'code' => '4300',
                'name' => 'Dessert Sales',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Revenue from dessert sales'
            ],
            [
                'code' => '4400',
                'name' => 'Delivery Fees',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Income from delivery charges'
            ],
            [
                'code' => '4500',
                'name' => 'Other Income',
                'type' => 'revenue',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Miscellaneous income'
            ],

            // Expense Accounts
            [
                'code' => '5000',
                'name' => 'Cost of Goods Sold',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Direct costs of products sold'
            ],
            [
                'code' => '5100',
                'name' => 'Food Ingredients',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Cost of sandwich ingredients'
            ],
            [
                'code' => '5200',
                'name' => 'Bread',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Cost of bread for sandwiches'
            ],
            [
                'code' => '5300',
                'name' => 'Beverage Costs',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Cost of drinks and water'
            ],
            [
                'code' => '5400',
                'name' => 'Dessert Costs',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Cost of dessert ingredients'
            ],
            [
                'code' => '5500',
                'name' => 'Packaging Supplies',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Boxes, bags, wraps, etc.'
            ],
            [
                'code' => '6000',
                'name' => 'Operating Expenses',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Day-to-day business expenses'
            ],
            [
                'code' => '6100',
                'name' => 'Salaries and Wages',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Staff salaries and wages'
            ],
            [
                'code' => '6200',
                'name' => 'Rent Expense',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Shop rental costs'
            ],
            [
                'code' => '6300',
                'name' => 'Utilities',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Electricity, water, gas (Sewa)'
            ],
            [
                'code' => '6400',
                'name' => 'Delivery Bike Rent',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Cost for delivery transportation'
            ],
            [
                'code' => '6500',
                'name' => 'Staff Accommodation',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Housing costs for employees'
            ],
            [
                'code' => '6600',
                'name' => 'Mobile and Internet',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Communication expenses'
            ],
            [
                'code' => '6700',
                'name' => 'Marketing and Advertising',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Media and promotional costs'
            ],
            [
                'code' => '6800',
                'name' => 'Licenses and Permits',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Legal permits and business licenses'
            ],
            [
                'code' => '6900',
                'name' => 'Visa and Immigration Costs',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Staff visa expenses'
            ],
            [
                'code' => '7000',
                'name' => 'General and Administrative',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => true,
                'opening_balance' => 0,
                'description' => 'Overhead expenses'
            ],
            [
                'code' => '7100',
                'name' => 'Office Supplies',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Paper, pens, etc.'
            ],
            [
                'code' => '7200',
                'name' => 'Professional Fees',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Accounting, legal fees'
            ],
            [
                'code' => '7300',
                'name' => 'Insurance',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Business insurance premiums'
            ],
            [
                'code' => '7400',
                'name' => 'Repairs and Maintenance',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Equipment and facility maintenance'
            ],
            [
                'code' => '7500',
                'name' => 'Depreciation Expense',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Periodic depreciation of assets'
            ],
            [
                'code' => '7600',
                'name' => 'Bank Charges',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Bank fees and service charges'
            ],
            [
                'code' => '7700',
                'name' => 'Miscellaneous Expenses',
                'type' => 'expense',
                'parent_account_id' => null,
                'is_system_account' => false,
                'opening_balance' => 0,
                'description' => 'Other uncategorized expenses'
            ],
        ];

        // First create all accounts with null parent_id
        $createdAccounts = [];
        foreach ($accounts as $accountData) {
            $account = Account::create($accountData);
            $createdAccounts[$accountData['code']] = $account;
        }

        // Now set up the parent-child relationships
        $parentChildRelationships = [
            // Assets
            '1100' => '1000', // Cash -> Current Assets
            '1110' => '1100', // Cash on Hand -> Cash
            '1120' => '1100', // Bank Accounts -> Cash
            '1121' => '1120', // Main Bank -> Bank Accounts
            '1122' => '1120', // Savings -> Bank Accounts
            '1200' => '1000', // AR -> Current Assets
            '1300' => '1000', // Inventory -> Current Assets
            '1400' => '1000', // Prepaid -> Current Assets
            '1510' => '1500', // Equipment -> Fixed Assets
            '1520' => '1500', // Accum Dep -> Fixed Assets

            // Liabilities
            '2100' => '2000', // AP -> Current Liab
            '2200' => '2000', // Accrued -> Current Liab
            '2300' => '2000', // Short loans -> Current Liab
            '2400' => '2000', // Tax payable -> Current Liab

            // Equity
            '3100' => '3000', // Partner 1 -> Equity
            '3200' => '3000', // Partner 2 -> Equity
            '3300' => '3000', // Partner 3 -> Equity
            '3400' => '3000', // Partner 4 -> Equity
            '3500' => '3000', // Partner 5 -> Equity
            '3600' => '3000', // Retained -> Equity
            '3700' => '3000', // Prior Loss -> Equity
            '3800' => '3000', // Drawings -> Equity

            // Revenue
            '4100' => '4000', // Sandwich -> Sales
            '4200' => '4000', // Drinks -> Sales
            '4300' => '4000', // Dessert -> Sales
            '4400' => '4000', // Delivery -> Sales

            // COGS
            '5100' => '5000', // Ingredients -> COGS
            '5200' => '5000', // Bread -> COGS
            '5300' => '5000', // Beverage -> COGS
            '5400' => '5000', // Dessert -> COGS
            '5500' => '5000', // Packaging -> COGS

            // Operating Expenses
            '6100' => '6000', // Salaries -> OpEx
            '6200' => '6000', // Rent -> OpEx
            '6300' => '6000', // Utilities -> OpEx
            '6400' => '6000', // Bike rent -> OpEx
            '6500' => '6000', // Staff accom -> OpEx
            '6600' => '6000', // Mobile -> OpEx
            '6700' => '6000', // Marketing -> OpEx
            '6800' => '6000', // Licenses -> OpEx
            '6900' => '6000', // Visas -> OpEx

            // G&A Expenses
            '7100' => '7000', // Office -> G&A
            '7200' => '7000', // Prof fees -> G&A
            '7300' => '7000', // Insurance -> G&A
            '7400' => '7000', // Repairs -> G&A
            '7500' => '7000', // Depreciation -> G&A
            '7600' => '7000', // Bank charges -> G&A
            '7700' => '7000', // Misc -> G&A
        ];

        foreach ($parentChildRelationships as $childCode => $parentCode) {
            if (isset($createdAccounts[$childCode]) && isset($createdAccounts[$parentCode])) {
                $createdAccounts[$childCode]->update([
                    'parent_account_id' => $createdAccounts[$parentCode]->id
                ]);
            }
        }
    }
}
