<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Account,
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
    Setup
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all tables
        $tables = [
            'accounts', 'tax_rates', 'restaurants', 'staff', 'menu_categories',
            'menu_items', 'suppliers', 'inventory_items', 'inventory_transactions',
            'tables', 'customers', 'orders', 'order_items', 'bank_accounts',
            'shifts', 'setups'
        ];

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed accounts (using your existing AccountsSeeder)
        $this->call(AccountsSeeder::class);

        // Create tax rates
        $taxRates = [
            ['name' => 'Standard VAT', 'rate' => 5.00, 'is_active' => true],
            ['name' => 'Tourism Fee', 'rate' => 2.00, 'is_active' => true],
            ['name' => 'Service Charge', 'rate' => 10.00, 'is_active' => false],
        ];

        foreach ($taxRates as $taxData) {
            TaxRate::create($taxData);
        }

        // Create restaurant
        $restaurant = Restaurant::create([
            'name' => 'Delicious Sandwich Co.',
            'legal_name' => 'Delicious Sandwich Co. LLC',
            'tax_id' => '123456789012345',
            'phone' => '+971501234567',
            'email' => 'info@delicious-sandwich.com',
            'website' => 'https://delicious-sandwich.com',
            'address' => '123 Sheikh Zayed Road, Dubai, UAE',
            'city' => 'Dubai',
            'country' => 'United Arab Emirates',
            'timezone' => 'Asia/Dubai',
            'currency' => 'AED',
            'default_tax_rate_id' => 1,
            'is_active' => true,
            'business_hours' => json_encode([
                'sunday' => ['open' => '08:00', 'close' => '22:00'],
                'monday' => ['open' => '08:00', 'close' => '22:00'],
                'tuesday' => ['open' => '08:00', 'close' => '22:00'],
                'wednesday' => ['open' => '08:00', 'close' => '22:00'],
                'thursday' => ['open' => '08:00', 'close' => '22:00'],
                'friday' => ['open' => '10:00', 'close' => '23:00'],
                'saturday' => ['open' => '10:00', 'close' => '23:00'],
            ])
        ]);

        // Create staff members
        $staff = [
            [
                'user_id' => 2, // Assuming you have a user with ID 1
                'restaurant_id' => $restaurant->id,
                'position' => 'Manager',
                'salary' => 8000.00,
                'passport_number' => 'A12345678',
                'passport_expiry_date' => '2025-12-31',
                'emirates_id_number' => '784-1980-1234567-1',
                'emirates_id_expiry_date' => '2025-12-31',
                'nationality' => 'Lebanese',
                'phone' => '+971501234568',
                'hire_date' => '2022-01-15',
            ],
            [
                'user_id' => 3,
                'restaurant_id' => $restaurant->id,
                'position' => 'Chef',
                'salary' => 5000.00,
                'passport_number' => 'B87654321',
                'passport_expiry_date' => '2024-06-30',
                'emirates_id_number' => '784-1990-7654321-2',
                'emirates_id_expiry_date' => '2024-06-30',
                'nationality' => 'Indian',
                'phone' => '+971501234569',
                'hire_date' => '2022-03-10',
            ],
            [
                'user_id' => 4,
                'restaurant_id' => $restaurant->id,
                'position' => 'Cashier',
                'salary' => 3500.00,
                'passport_number' => 'C11223344',
                'passport_expiry_date' => '2024-09-15',
                'emirates_id_number' => '784-1995-1122334-3',
                'emirates_id_expiry_date' => '2024-09-15',
                'nationality' => 'Filipino',
                'phone' => '+971501234570',
                'hire_date' => '2022-05-20',
            ],
        ];

        foreach ($staff as $staffData) {
            Staff::create($staffData);
        }

        // Create menu categories
        $categories = [
            ['name' => 'Sandwiches', 'restaurant_id' => $restaurant->id, 'sort_order' => 1],
            ['name' => 'Drinks', 'restaurant_id' => $restaurant->id, 'sort_order' => 2],
            ['name' => 'Desserts', 'restaurant_id' => $restaurant->id, 'sort_order' => 3],
            ['name' => 'Combos', 'restaurant_id' => $restaurant->id, 'sort_order' => 4],
        ];

        foreach ($categories as $categoryData) {
            MenuCategory::create($categoryData);
        }

        // Create menu items
        $menuItems = [
            // Sandwiches
            [
                'category_id' => 1,
                'name' => 'Classic Sandwich',
                'description' => 'Our signature sandwich with fresh ingredients',
                'price' => 25.00,
                'cost' => 8.50,
                'is_taxable' => true,
                'tax_rate_id' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'Chicken Avocado',
                'description' => 'Grilled chicken with fresh avocado',
                'price' => 30.00,
                'cost' => 10.00,
                'is_taxable' => true,
                'tax_rate_id' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'Veggie Delight',
                'description' => 'Vegetarian option with fresh veggies',
                'price' => 22.00,
                'cost' => 7.00,
                'is_taxable' => true,
                'tax_rate_id' => 1,
            ],

            // Drinks
            [
                'category_id' => 2,
                'name' => 'Mineral Water',
                'description' => '500ml bottled water',
                'price' => 5.00,
                'cost' => 1.00,
                'is_taxable' => false,
            ],
            [
                'category_id' => 2,
                'name' => 'Fresh Juice',
                'description' => 'Seasonal fresh juice',
                'price' => 15.00,
                'cost' => 4.00,
                'is_taxable' => true,
                'tax_rate_id' => 1,
            ],

            // Desserts
            [
                'category_id' => 3,
                'name' => 'Chocolate Cake',
                'description' => 'Homemade chocolate cake slice',
                'price' => 18.00,
                'cost' => 5.00,
                'is_taxable' => true,
                'tax_rate_id' => 1,
            ],
        ];

        foreach ($menuItems as $itemData) {
            MenuItem::create($itemData);
        }

        // Create suppliers
        $suppliers = [
            [
                'name' => 'Fresh Food Supplies',
                'contact_person' => 'Ahmed Mohamed',
                'phone' => '+971501111111',
                'email' => 'ahmed@freshfood.ae',
                'address' => 'Industrial Area, Dubai',
                'tax_id' => '1000000001',
            ],
            [
                'name' => 'Beverage Distributors',
                'contact_person' => 'John Smith',
                'phone' => '+971502222222',
                'email' => 'john@beveragedist.com',
                'address' => 'Jebel Ali, Dubai',
                'tax_id' => '1000000002',
            ],
            [
                'name' => 'Bakery Ingredients LLC',
                'contact_person' => 'Fatima Ali',
                'phone' => '+971503333333',
                'email' => 'fatima@bakeryingredients.ae',
                'address' => 'Sharjah Industrial Area',
                'tax_id' => '1000000003',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create inventory items
        $inventoryItems = [
            [
                'name' => 'Chicken Breast',
                'unit' => 'kg',
                'current_quantity' => 50,
                'alert_quantity' => 10,
            ],
            [
                'name' => 'Avocado',
                'unit' => 'piece',
                'current_quantity' => 100,
                'alert_quantity' => 20,
            ],
            [
                'name' => 'Mineral Water',
                'unit' => 'bottle',
                'current_quantity' => 200,
                'alert_quantity' => 50,
            ],
            [
                'name' => 'Bread',
                'unit' => 'loaf',
                'current_quantity' => 30,
                'alert_quantity' => 5,
            ],
        ];

        foreach ($inventoryItems as $itemData) {
            InventoryItem::create($itemData);
        }

        // Create inventory transactions
        $inventoryTransactions = [
            [
                'inventory_item_id' => 1,
                'supplier_id' => 1,
                'type' => InventoryTransactionTypeEnum::IN,
                'quantity' => 50,
                'unit_cost' => 15.00,
                'user_id' => 1,
                'notes' => 'Initial stock',
            ],
            [
                'inventory_item_id' => 2,
                'supplier_id' => 1,
                'type' => InventoryTransactionTypeEnum::IN,
                'quantity' => 100,
                'unit_cost' => 3.50,
                'user_id' => 1,
                'notes' => 'Initial stock',
            ],
            [
                'inventory_item_id' => 3,
                'supplier_id' => 2,
                'type' => InventoryTransactionTypeEnum::IN,
                'quantity' => 200,
                'unit_cost' => 1.00,
                'user_id' => 1,
                'notes' => 'Initial stock',
            ],
            [
                'inventory_item_id' => 4,
                'supplier_id' => 3,
                'type' => InventoryTransactionTypeEnum::IN,
                'quantity' => 30,
                'unit_cost' => 2.50,
                'user_id' => 1,
                'notes' => 'Initial stock',
            ],
        ];

        foreach ($inventoryTransactions as $transactionData) {
            InventoryTransaction::create($transactionData);
        }

        // Create tables
        $tables = [
            ['name' => 'Table 1', 'restaurant_id' => $restaurant->id, 'capacity' => 4, 'status' => TableStatusEnum::AVAILABLE],
            ['name' => 'Table 2', 'restaurant_id' => $restaurant->id, 'capacity' => 4, 'status' => TableStatusEnum::AVAILABLE],
            ['name' => 'Table 3', 'restaurant_id' => $restaurant->id, 'capacity' => 6, 'status' => TableStatusEnum::AVAILABLE],
            ['name' => 'Table 4', 'restaurant_id' => $restaurant->id, 'capacity' => 2, 'status' => TableStatusEnum::AVAILABLE],
            ['name' => 'Table 5', 'restaurant_id' => $restaurant->id, 'capacity' => 8, 'status' => TableStatusEnum::RESERVED],
        ];

        foreach ($tables as $tableData) {
            Table::create($tableData);
        }

        // Create customers
        $customers = [
            [
                'name' => 'Regular Customer',
                'email' => 'customer1@example.com',
                'phone' => '+971501234500',
            ],
            [
                'name' => 'Corporate Customer',
                'email' => 'business@company.com',
                'phone' => '+971501234501',
                'tax_id' => '2000000001',
            ],
            [
                'name' => 'Tourist Customer',
                'email' => 'tourist@example.com',
                'phone' => '+971501234502',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create orders
        $orders = [
            [
                'table_id' => 1,
                'user_id' => 3, // Cashier
                'customer_id' => 1,
                'staff_id' => 2, // Chef
                'status' => OrderStatusEnum::COMPLETED,
                'subtotal' => 50.00,
                'tax_amount' => 2.50,
                'discount_amount' => 0.00,
                'total' => 52.50,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'table_id' => 5,
                'user_id' => 3,
                'customer_id' => 2,
                'staff_id' => 2,
                'status' => OrderStatusEnum::PREPARING,
                'subtotal' => 120.00,
                'tax_amount' => 6.00,
                'discount_amount' => 10.00,
                'total' => 116.00,
                'created_at' => Carbon::now()->subHours(1),
            ],
            [
                'table_id' => null, // Delivery order
                'user_id' => 1, // Manager
                'customer_id' => 3,
                'staff_id' => 2,
                'status' => OrderStatusEnum::DELIVERED,
                'subtotal' => 75.00,
                'tax_amount' => 3.75,
                'discount_amount' => 0.00,
                'total' => 78.75,
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

               // Create bank accounts
        $bankAccounts = [
            [
                'account_id' => Account::where('code', '1121')->first()->id,
                'bank_name' => 'Emirates NBD',
                'account_number' => '123456789',
                'current_balance' => 50000.00,
            ],
            [
                'account_id' => Account::where('code', '1122')->first()->id,
                'bank_name' => 'Mashreq Bank',
                'account_number' => '987654321',
                'current_balance' => 20000.00,
            ],
        ];

        foreach ($bankAccounts as $accountData) {
            BankAccount::create($accountData);
        }


        // Create shifts
        $shifts = [
            [
                'staff_id' => 2, // Chef
                'start_time' => Carbon::now()->subDays(1)->setTime(8, 0),
                'end_time' => Carbon::now()->subDays(1)->setTime(16, 0),
                'total_earnings' => 0.00,
                'notes' => 'Morning shift',
            ],
            [
                'staff_id' => 3, // Cashier
                'start_time' => Carbon::now()->subDays(1)->setTime(12, 0),
                'end_time' => Carbon::now()->subDays(1)->setTime(20, 0),
                'total_earnings' => 0.00,
                'notes' => 'Afternoon shift',
            ],
            [
                'staff_id' => 2, // Chef
                'start_time' => Carbon::now()->setTime(8, 0),
                'total_earnings' => 0.00,
                'notes' => 'Current shift - in progress',
            ],
        ];

        foreach ($shifts as $shiftData) {
            Shift::create($shiftData);
        }

        // Create setups
        $setups = [
            ['name' => 'Accounts Receivable', 'type' => 'account', 'account_id' => Account::where('code', '1200')->first()->id],
            ['name' => 'Accounts Payable', 'type' => 'account', 'account_id' => Account::where('code', '2100')->first()->id],
            ['name' => 'Cash Account', 'type' => 'account', 'account_id' => Account::where('code', '1110')->first()->id],
        ];

        foreach ($setups as $setupData) {
            Setup::create($setupData);
        }
        Account::fixTree();
    }



}
