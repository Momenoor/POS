<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyData extends Seeder
{
    // Account IDs mapping based on your chart of accounts
    protected $accountIds = [
        'cash_on_hand' => 3,
        'main_bank_account' => 5,
        'accounts_receivable' => 7,
        'inventory' => 8,
        'accounts_payable' => 14,
        'sales_tax_payable' => 17,
        'food_sales' => 28,
        'beverage_sales' => 29,
        'dessert_sales' => 30,
        'delivery_fees' => 31,
        'food_cogs' => 34,
        'beverage_cogs' => 36,
        'dessert_cogs' => 37,
        'packaging_supplies' => 38,
        'salaries_wages' => 40,
        'rent_expense' => 41,
        'utilities' => 42,
        'marketing' => 47,
    ];

    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        $this->truncateTables();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed users first as they're referenced by other tables
        $this->seedTaxRates();
        $this->seedUsers();
        $this->seedRestaurants();
        $this->seedStaff();
        $this->seedMenuCategories();
        $this->seedMenuItems();
        $this->seedSuppliers();
        $this->seedInventoryItems();
        $this->seedInventoryTransactions();
        $this->seedTables();
        $this->seedCustomers();
        $this->seedOrders();
        $this->seedOrderItems();
        $this->seedJournalEntries();
        $this->seedJournalItems();
        $this->seedExpenses();
        $this->seedPayments();
        $this->seedBankAccounts();
        $this->seedBankReconciliations();
        $this->seedBankTransactions();
        $this->seedShifts();
        $this->seedItemAccounts();
    }

    protected function truncateTables()
    {
        $tables = [
            'accounts_setup',
            'shifts',
            'bank_transactions',
            'bank_reconciliations',
            'bank_accounts',
            'payments',
            'expenses',
            'journal_items',
            'journal_entries',
            'order_items',
            'orders',
            'customers',
            'tables',
            'inventory_transactions',
            'inventory_items',
            'suppliers',
            'menu_items',
            'menu_categories',
            'staff',
            'tax_rates',
            'restaurants',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    protected function seedUsers()
    {
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
    }

    protected function seedRestaurants()
    {
        DB::table('restaurants')->insert([
            [
                'name' => 'Fine Dining Restaurant',
                'legal_name' => 'Fine Dining FZ LLC',
                'tax_id' => '123456789',
                'phone' => '+97141234567',
                'email' => 'info@finedining.ae',
                'website' => 'https://finedining.ae',
                'address' => '123 Sheikh Zayed Road, Dubai, UAE',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'timezone' => 'Asia/Dubai',
                'currency' => 'AED',
                'logo_path' => 'logos/restaurant1.jpg',
                'business_hours' => json_encode([
                    'monday' => ['open' => '09:00', 'close' => '23:00'],
                    'tuesday' => ['open' => '09:00', 'close' => '23:00'],
                    'wednesday' => ['open' => '09:00', 'close' => '23:00'],
                    'thursday' => ['open' => '09:00', 'close' => '00:00'],
                    'friday' => ['open' => '12:00', 'close' => '00:00'],
                    'saturday' => ['open' => '12:00', 'close' => '00:00'],
                    'sunday' => ['open' => '09:00', 'close' => '23:00'],
                ]),
                'default_tax_rate_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casual Eats',
                'legal_name' => 'Casual Eats FZ LLC',
                'tax_id' => '987654321',
                'phone' => '+97142234567',
                'email' => 'info@casualeats.ae',
                'website' => 'https://casualeats.ae',
                'address' => '456 Al Wasl Road, Dubai, UAE',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'timezone' => 'Asia/Dubai',
                'currency' => 'AED',
                'logo_path' => 'logos/restaurant2.jpg',
                'business_hours' => json_encode([
                    'monday' => ['open' => '08:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '22:00'],
                    'thursday' => ['open' => '08:00', 'close' => '23:00'],
                    'friday' => ['open' => '10:00', 'close' => '23:00'],
                    'saturday' => ['open' => '10:00', 'close' => '23:00'],
                    'sunday' => ['open' => '08:00', 'close' => '22:00'],
                ]),
                'default_tax_rate_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedStaff()
    {
        DB::table('staff')->insert([
            [
                'user_id' => 2, // Manager
                'restaurant_id' => 1,
                'position' => 'Manager',
                'hourly_rate' => 50.00,
                'hire_date' => Carbon::now()->subYear(),
                'termination_date' => null,
                'notes' => 'Senior manager with 5 years experience',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3, // Staff
                'restaurant_id' => 1,
                'position' => 'Waiter',
                'hourly_rate' => 20.00,
                'hire_date' => Carbon::now()->subMonths(6),
                'termination_date' => null,
                'notes' => 'Part-time waiter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4, // Cashier
                'restaurant_id' => 1,
                'position' => 'Cashier',
                'hourly_rate' => 25.00,
                'hire_date' => Carbon::now()->subMonths(3),
                'termination_date' => null,
                'notes' => 'Main cashier for evening shifts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedTaxRates()
    {
        DB::table('tax_rates')->insert([
            [
                'name' => 'VAT 5%',
                'rate' => 5.00,
                'is_active' => true,
                'description' => 'Standard UAE VAT rate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tourism Fee 10%',
                'rate' => 10.00,
                'is_active' => true,
                'description' => 'Dubai tourism fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedMenuCategories()
    {
        DB::table('menu_categories')->insert([
            [
                'restaurant_id' => 1,
                'name' => 'Starters',
                'description' => 'Appetizers and small dishes',
                'sort_order' => 1,
                'is_active' => true,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Main Courses',
                'description' => 'Main dishes and entrees',
                'sort_order' => 2,
                'is_active' => true,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Desserts',
                'description' => 'Sweet treats to finish your meal',
                'sort_order' => 3,
                'is_active' => true,
                'account_id' => $this->accountIds['dessert_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Beverages',
                'description' => 'Drinks and refreshments',
                'sort_order' => 4,
                'is_active' => true,
                'account_id' => $this->accountIds['beverage_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedMenuItems()
    {
        DB::table('menu_items')->insert([
            // Starters
            [
                'category_id' => 1,
                'name' => 'Bruschetta',
                'description' => 'Toasted bread topped with tomatoes, garlic, and fresh basil',
                'price' => 35.00,
                'cost' => 10.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/bruschetta.jpg',
                'options' => json_encode(['add_cheese' => 5.00]),
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'name' => 'Caesar Salad',
                'description' => 'Classic Caesar salad with romaine lettuce and croutons',
                'price' => 45.00,
                'cost' => 12.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/caesar.jpg',
                'options' => json_encode(['add_chicken' => 15.00, 'add_shrimp' => 25.00]),
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Main Courses
            [
                'category_id' => 2,
                'name' => 'Grilled Salmon',
                'description' => 'Fresh salmon fillet with lemon butter sauce',
                'price' => 120.00,
                'cost' => 40.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/salmon.jpg',
                'options' => json_encode(['add_side' => 10.00]),
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'name' => 'Ribeye Steak',
                'description' => '12oz prime ribeye with mashed potatoes',
                'price' => 150.00,
                'cost' => 50.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/ribeye.jpg',
                'options' => json_encode(['doneness' => ['medium-rare', 'medium', 'well-done']]),
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Desserts
            [
                'category_id' => 3,
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm chocolate cake with molten center and vanilla ice cream',
                'price' => 45.00,
                'cost' => 12.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/lava-cake.jpg',
                'options' => null,
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['dessert_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Beverages
            [
                'category_id' => 4,
                'name' => 'Sparkling Water',
                'description' => 'Premium imported sparkling water',
                'price' => 15.00,
                'cost' => 3.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/sparkling-water.jpg',
                'options' => null,
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['beverage_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4,
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 25.00,
                'cost' => 7.00,
                'is_taxable' => true,
                'is_available' => true,
                'image' => 'menu/orange-juice.jpg',
                'options' => null,
                'tax_rate_id' => 1,
                'account_id' => $this->accountIds['beverage_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedSuppliers()
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'Fresh Produce Co.',
                'contact_person' => 'Ahmed Mohammed',
                'phone' => '+971501234567',
                'email' => 'ahmed@freshproduce.ae',
                'address' => 'Industrial Area, Dubai',
                'tax_id' => 'SUP123456',
                'account_id' => $this->accountIds['accounts_payable'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beverage Distributors',
                'contact_person' => 'Fatima Al Mansoori',
                'phone' => '+971502345678',
                'email' => 'fatima@beveragedist.ae',
                'address' => 'Jebel Ali Free Zone, Dubai',
                'tax_id' => 'SUP234567',
                'account_id' => $this->accountIds['accounts_payable'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedInventoryItems()
    {
        DB::table('inventory_items')->insert([
            [
                'name' => 'Salmon Fillet',
                'unit' => 'kg',
                'current_quantity' => 15.5,
                'alert_quantity' => 5.0,
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ribeye Steak',
                'unit' => 'kg',
                'current_quantity' => 12.0,
                'alert_quantity' => 4.0,
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sparkling Water',
                'unit' => 'bottle',
                'current_quantity' => 120,
                'alert_quantity' => 30,
                'supplier_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedInventoryTransactions()
    {
        DB::table('inventory_transactions')->insert([
            [
                'inventory_item_id' => 1,
                'supplier_id' => 1,
                'type' => 'purchase',
                'quantity' => 20.0,
                'unit_cost' => 30.0,
                'user_id' => 2,
                'notes' => 'Initial stock purchase',
                'referenceable_type' => 'App\Models\Purchase',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'inventory_item_id' => 2,
                'supplier_id' => 1,
                'type' => 'purchase',
                'quantity' => 15.0,
                'unit_cost' => 40.0,
                'user_id' => 2,
                'notes' => 'Initial stock purchase',
                'referenceable_type' => 'App\Models\Purchase',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'inventory_item_id' => 3,
                'supplier_id' => 2,
                'type' => 'purchase',
                'quantity' => 150,
                'unit_cost' => 2.5,
                'user_id' => 2,
                'notes' => 'Initial stock purchase',
                'referenceable_type' => 'App\Models\Purchase',
                'referenceable_id' => 3,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'inventory_item_id' => 1,
                'supplier_id' => null,
                'type' => 'consumption',
                'quantity' => 4.5,
                'unit_cost' => null,
                'user_id' => 3,
                'notes' => 'Dinner service usage',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ]);
    }

    protected function seedTables()
    {
        DB::table('tables')->insert([
            [
                'restaurant_id' => 1,
                'name' => 'Table 1',
                'capacity' => 4,
                'status' => 'available',
                'notes' => 'Window view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Table 2',
                'capacity' => 4,
                'status' => 'available',
                'notes' => 'Window view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Table 3',
                'capacity' => 6,
                'status' => 'available',
                'notes' => 'Center of dining area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'restaurant_id' => 1,
                'name' => 'VIP Booth 1',
                'capacity' => 8,
                'status' => 'available',
                'notes' => 'Private booth',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedCustomers()
    {
        DB::table('customers')->insert([
            [
                'name' => 'Regular Customer',
                'email' => 'customer@example.com',
                'phone' => '+971501112222',
                'address' => '123 Business Bay, Dubai',
                'tax_id' => 'CUST12345',
                'account_id' => $this->accountIds['accounts_receivable'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Corporate Account',
                'email' => 'corporate@example.com',
                'phone' => '+971502223333',
                'address' => '456 DIFC, Dubai',
                'tax_id' => 'CUST23456',
                'account_id' => $this->accountIds['accounts_receivable'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedOrders()
    {
        DB::table('orders')->insert([
            [
                'table_id' => 1,
                'user_id' => 3,
                'customer_id' => 1,
                'staff_id' => 2,
                'status' => 'completed',
                'subtotal' => 190.00,
                'tax_amount' => 10.00,
                'discount_amount' => 0.00,
                'total' => 200.00,
                'payment_method' => 'cash',
                'is_paid' => true,
                'notes' => 'Birthday celebration',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'table_id' => 3,
                'user_id' => 4,
                'customer_id' => 2,
                'staff_id' => 3,
                'status' => 'completed',
                'subtotal' => 340.00,
                'tax_amount' => 16.00,
                'discount_amount' => 20.00,
                'total' => 336.00,
                'payment_method' => 'credit_card',
                'is_paid' => true,
                'notes' => 'Corporate dinner',
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedOrderItems()
    {
        DB::table('order_items')->insert([
            // Order 1 items
            [
                'order_id' => 1,
                'menu_item_id' => 1,
                'unit_price' => 35.00,
                'quantity' => 2,
                'subtotal' => 70.00,
                'special_instructions' => 'No garlic in bruschetta',
                'selected_options' => json_encode(['add_cheese' => 5.00]),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'order_id' => 1,
                'menu_item_id' => 3,
                'unit_price' => 130.00,
                'quantity' => 1,
                'subtotal' => 130.00,
                'special_instructions' => 'Well done',
                'selected_options' => json_encode(['add_side' => 10.00]),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],

            // Order 2 items
            [
                'order_id' => 2,
                'menu_item_id' => 4,
                'unit_price' => 150.00,
                'quantity' => 2,
                'subtotal' => 300.00,
                'special_instructions' => 'One medium, one medium-rare',
                'selected_options' => null,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'order_id' => 2,
                'menu_item_id' => 6,
                'unit_price' => 25.00,
                'quantity' => 2,
                'subtotal' => 50.00,
                'special_instructions' => 'No ice',
                'selected_options' => null,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedJournalEntries()
    {
        DB::table('journal_entries')->insert([
            [
                'entry_date' => Carbon::now()->subDays(2),
                'reference_number' => 'JE-' . Carbon::now()->subDays(2)->format('Ymd') . '-001',
                'memo' => 'Sales for ' . Carbon::now()->subDays(2)->format('Y-m-d'),
                'created_by' => 2,
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'entry_date' => Carbon::now()->subDay(),
                'reference_number' => 'JE-' . Carbon::now()->subDay()->format('Ymd') . '-001',
                'memo' => 'Sales for ' . Carbon::now()->subDay()->format('Y-m-d'),
                'created_by' => 2,
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedJournalItems()
    {
        DB::table('journal_items')->insert([
            // Journal Entry 1 items (Order 1)
            [
                'journal_entry_id' => 1,
                'account_id' => $this->accountIds['cash_on_hand'],
                'debit' => 200.00,
                'credit' => 0.00,
                'memo' => 'Cash received for order #1',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'journal_entry_id' => 1,
                'account_id' => $this->accountIds['food_sales'],
                'debit' => 0.00,
                'credit' => 190.00,
                'memo' => 'Food sales revenue',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'journal_entry_id' => 1,
                'account_id' => $this->accountIds['sales_tax_payable'],
                'debit' => 0.00,
                'credit' => 10.00,
                'memo' => 'Sales tax collected',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],

            // Journal Entry 2 items (Order 2)
            [
                'journal_entry_id' => 2,
                'account_id' => $this->accountIds['main_bank_account'],
                'debit' => 316.00,
                'credit' => 0.00,
                'memo' => 'Credit card payment received for order #2',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'journal_entry_id' => 2,
                'account_id' => $this->accountIds['food_sales'],
                'debit' => 0.00,
                'credit' => 300.00,
                'memo' => 'Food sales revenue',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'journal_entry_id' => 2,
                'account_id' => $this->accountIds['beverage_sales'],
                'debit' => 0.00,
                'credit' => 20.00,
                'memo' => 'Beverage sales revenue',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'journal_entry_id' => 2,
                'account_id' => $this->accountIds['sales_tax_payable'],
                'debit' => 0.00,
                'credit' => 16.00,
                'memo' => 'Sales tax collected',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'journal_entry_id' => 2,
                'account_id' => $this->accountIds['food_sales'],
                'debit' => 20.00,
                'credit' => 0.00,
                'memo' => 'Discount applied',
                'referenceable_type' => 'App\Models\Order',
                'referenceable_id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedExpenses()
    {
        DB::table('expenses')->insert([
            [
                'account_id' => $this->accountIds['food_cogs'],
                'supplier_id' => 1,
                'user_id' => 2,
                'amount' => 600.00,
                'date' => Carbon::now()->subDays(7),
                'description' => 'Salmon and ribeye purchase',
                'is_paid' => true,
                'payment_method' => 'bank_transfer',
                'reference' => 'INV-12345',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'account_id' => $this->accountIds['beverage_cogs'],
                'supplier_id' => 2,
                'user_id' => 2,
                'amount' => 375.00,
                'date' => Carbon::now()->subDays(7),
                'description' => 'Sparkling water purchase',
                'is_paid' => false,
                'payment_method' => null,
                'reference' => 'INV-67890',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'account_id' => $this->accountIds['rent_expense'],
                'supplier_id' => null,
                'user_id' => 2,
                'amount' => 15000.00,
                'date' => Carbon::now()->subDays(5),
                'description' => 'Monthly rent payment',
                'is_paid' => true,
                'payment_method' => 'bank_transfer',
                'reference' => 'RENT-0424',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ]);
    }

    protected function seedPayments()
    {
        DB::table('payments')->insert([
            [
                'payable_type' => 'App\Models\Expense',
                'payable_id' => 1,
                'referenceable_type' => 'App\Models\BankTransaction',
                'referenceable_id' => 1,
                'amount' => 600.00,
                'payment_date' => Carbon::now()->subDays(7),
                'payment_method' => 'bank_transfer',
                'reference' => 'BT-12345',
                'user_id' => 2,
                'notes' => 'Payment for food supplies',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'payable_type' => 'App\Models\Expense',
                'payable_id' => 3,
                'referenceable_type' => 'App\Models\BankTransaction',
                'referenceable_id' => 2,
                'amount' => 15000.00,
                'payment_date' => Carbon::now()->subDays(5),
                'payment_method' => 'bank_transfer',
                'reference' => 'BT-12346',
                'user_id' => 2,
                'notes' => 'Monthly rent payment',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ]);
    }

    protected function seedBankAccounts()
    {
        DB::table('bank_accounts')->insert([
            [
                'account_id' => $this->accountIds['main_bank_account'],
                'bank_name' => 'Emirates NBD',
                'account_number' => '123456789',
                'routing_number' => 'EBILAEAD',
                'current_balance' => 50000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'account_id' => $this->accountIds['cash_on_hand'],
                'bank_name' => 'Cash Register',
                'account_number' => 'CASH-001',
                'routing_number' => null,
                'current_balance' => 2500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedBankReconciliations()
    {
        DB::table('bank_reconciliations')->insert([
            [
                'bank_account_id' => 1,
                'statement_date' => Carbon::now()->subMonth()->endOfMonth(),
                'statement_balance' => 45000.00,
                'adjusted_balance' => 45000.00,
                'is_completed' => true,
                'notes' => 'March 2024 reconciliation',
                'created_at' => Carbon::now()->subMonth()->endOfMonth(),
                'updated_at' => Carbon::now()->subMonth()->endOfMonth(),
            ],
        ]);
    }

    protected function seedBankTransactions()
    {
        DB::table('bank_transactions')->insert([
            [
                'bank_account_id' => 1,
                'reconciliation_id' => 1,
                'transaction_date' => Carbon::now()->subDays(7),
                'description' => 'Payment to Fresh Produce Co.',
                'amount' => 600.00,
                'type' => 'debit',
                'reference' => 'BT-12345',
                'is_reconciled' => true,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'bank_account_id' => 1,
                'reconciliation_id' => 1,
                'transaction_date' => Carbon::now()->subDays(5),
                'description' => 'Rent payment',
                'amount' => 15000.00,
                'type' => 'debit',
                'reference' => 'BT-12346',
                'is_reconciled' => true,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'bank_account_id' => 1,
                'reconciliation_id' => null,
                'transaction_date' => Carbon::now()->subDay(),
                'description' => 'Customer payment - Order #2',
                'amount' => 316.00,
                'type' => 'credit',
                'reference' => 'POS-002',
                'is_reconciled' => false,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedShifts()
    {
        DB::table('shifts')->insert([
            [
                'staff_id' => 2,
                'start_time' => Carbon::now()->subDays(2)->setTime(9, 0),
                'end_time' => Carbon::now()->subDays(2)->setTime(17, 0),
                'total_earnings' => 400.00,
                'notes' => 'Regular shift',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'staff_id' => 3,
                'start_time' => Carbon::now()->subDay()->setTime(17, 0),
                'end_time' => Carbon::now()->subDay()->setTime(23, 0),
                'total_earnings' => 120.00,
                'notes' => 'Evening shift',
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }

    protected function seedItemAccounts()
    {
        DB::table('accounts_setup')->insert([
            [
                'name' => 'Food Sales',
                'type' => 'menu_category',
                'account_id' => $this->accountIds['food_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beverage Sales',
                'type' => 'menu_category',
                'account_id' => $this->accountIds['beverage_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dessert Sales',
                'type' => 'menu_category',
                'account_id' => $this->accountIds['dessert_sales'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Food COGS',
                'type' => 'inventory_item',
                'account_id' => $this->accountIds['food_cogs'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beverage COGS',
                'type' => 'inventory_item',
                'account_id' => $this->accountIds['beverage_cogs'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salaries Expense',
                'type' => 'expense',
                'account_id' => $this->accountIds['salaries_wages'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
