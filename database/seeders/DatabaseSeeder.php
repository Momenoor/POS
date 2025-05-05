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
            'tables', 'customers', 'orders', 'order_items', 'journal_entries',
            'journal_items', 'bills', 'expenses', 'payments', 'bank_accounts',
            'bank_reconciliations', 'bank_transactions', 'shifts', 'setups'
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
                'payment_method' => PaymentMethodEnum::CASH,
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
                'payment_method' => PaymentMethodEnum::CREDIT_CARD,
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }

        // Create order items
        $orderItems = [
            // Order 1
            [
                'order_id' => 1,
                'menu_item_id' => 1, // Classic Sandwich
                'unit_price' => 25.00,
                'quantity' => 2,
                'subtotal' => 50.00,
            ],

            // Order 2
            [
                'order_id' => 2,
                'menu_item_id' => 2, // Chicken Avocado
                'unit_price' => 30.00,
                'quantity' => 2,
                'subtotal' => 60.00,
            ],
            [
                'order_id' => 2,
                'menu_item_id' => 5, // Fresh Juice
                'unit_price' => 15.00,
                'quantity' => 4,
                'subtotal' => 60.00,
            ],

            // Order 3
            [
                'order_id' => 3,
                'menu_item_id' => 3, // Veggie Delight
                'unit_price' => 22.00,
                'quantity' => 2,
                'subtotal' => 44.00,
            ],
            [
                'order_id' => 3,
                'menu_item_id' => 6, // Chocolate Cake
                'unit_price' => 18.00,
                'quantity' => 1,
                'subtotal' => 18.00,
            ],
            [
                'order_id' => 3,
                'menu_item_id' => 4, // Mineral Water
                'unit_price' => 5.00,
                'quantity' => 2,
                'subtotal' => 10.00,
            ],
        ];

        foreach ($orderItems as $itemData) {
            OrderItem::create($itemData);
        }

        // Create journal entries for orders
        foreach ($orders as $order) {
            if ($order['status'] === OrderStatusEnum::COMPLETED->value || $order['status'] === OrderStatusEnum::DELIVERED->value) {
                JournalEntry::create([
                    'entry_date' => $order['created_at'],
                    'reference_number' => 'ORD-' . $order['id'],
                    'memo' => 'Order #' . $order['id'],
                    'user_id' => $order['user_id'],
                    'referenceable_type' => Order::class,
                    'referenceable_id' => $order['id'],
                ]);
            }
        }

        // Create bills
        $bills = [
            [
                'reference_number' => 'BILL-001',
                'description' => 'Monthly food supplies',
                'date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'supplier_id' => 1,
                'user_id' => 1,
                'total' => 2500.00,
            ],
            [
                'reference_number' => 'BILL-002',
                'description' => 'Beverage order',
                'date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(25),
                'supplier_id' => 2,
                'user_id' => 1,
                'total' => 1200.00,
            ],
        ];

        foreach ($bills as $billData) {
            Bill::create($billData);
        }

        // Create expenses
        $expenses = [
            [
                'account_id' => Account::where('code', '5100')->first()->id, // Food Ingredients
                'bill_id' => 1,
                'inventory_item_id' => 1, // Chicken Breast
                'date' => Carbon::now()->subDays(10),
                'quantity' => 50,
                'unit_cost' => 15.00,
                'total' => 750.00,
                'notes' => 'Chicken breast purchase',
            ],
            [
                'account_id' => Account::where('code', '5100')->first()->id, // Food Ingredients
                'bill_id' => 1,
                'inventory_item_id' => 2, // Avocado
                'date' => Carbon::now()->subDays(10),
                'quantity' => 100,
                'unit_cost' => 3.50,
                'total' => 350.00,
                'notes' => 'Avocado purchase',
            ],
            [
                'account_id' => Account::where('code', '5300')->first()->id, // Beverage Costs
                'bill_id' => 2,
                'date' => Carbon::now()->subDays(5),
                'quantity' => 200,
                'unit_cost' => 1.00,
                'total' => 200.00,
                'notes' => 'Water purchase',
            ],
            [
                'account_id' => Account::where('code', '5200')->first()->id, // Bread
                'bill_id' => 1,
                'date' => Carbon::now()->subDays(10),
                'quantity' => 30,
                'unit_cost' => 2.50,
                'total' => 75.00,
                'notes' => 'Bread purchase',
            ],
            // Direct expense (not linked to bill)
            [
                'account_id' => Account::where('code', '6200')->first()->id, // Rent Expense
                'date' => Carbon::now()->subDays(1),
                'quantity' => 1,
                'unit_cost' => 15000.00,
                'total' => 15000.00,
                'notes' => 'Monthly rent payment',
            ],
        ];

        foreach ($expenses as $expenseData) {
            Expense::create($expenseData);
        }

        // Create payments
        $payments = [
            // Order payments
            [
                'payable_type' => Order::class,
                'payable_id' => 1,
                'amount' => 52.50,
                'payment_date' => Carbon::now()->subDays(2),
                'payment_method' => PaymentMethodEnum::CASH,
                'user_id' => 3,
                'notes' => 'Full payment for order #1',
            ],
            [
                'payable_type' => Order::class,
                'payable_id' => 3,
                'amount' => 78.75,
                'payment_date' => Carbon::now()->subDays(1),
                'payment_method' => PaymentMethodEnum::CREDIT_CARD,
                'user_id' => 1,
                'notes' => 'Card payment for delivery order',
            ],

            // Bill payment
            [
                'payable_type' => Bill::class,
                'payable_id' => 1,
                'amount' => 1175.00,
                'payment_date' => Carbon::now()->subDays(5),
                'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
                'user_id' => 1,
                'reference' => 'TRANS-12345',
                'notes' => 'Partial payment for food supplies',
            ],
        ];

        foreach ($payments as $paymentData) {
            Payment::create($paymentData);
        }

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

        // Create bank transactions
        $bankTransactions = [
            [
                'bank_account_id' => 1,
                'account_id' => Account::where('code', '1200')->first()->id, // Accounts Receivable
                'date' => Carbon::now()->subDays(1),
                'description' => 'Customer payment',
                'amount' => 78.75,
                'type' => BankTransactionTypeEnum::DEPOSIT,
                'reference' => 'ORD-3',
                'user_id' => 1,
            ],
            [
                'bank_account_id' => 1,
                'account_id' => Account::where('code', '2100')->first()->id, // Accounts Payable
                'date' => Carbon::now()->subDays(5),
                'description' => 'Supplier payment',
                'amount' => 1175.00,
                'type' => BankTransactionTypeEnum::WITHDRAWAL,
                'reference' => 'BILL-1',
                'user_id' => 1,
            ],
            [
                'bank_account_id' => 1,
                'account_id' => Account::where('code', '6200')->first()->id, // Rent Expense
                'date' => Carbon::now()->subDays(1),
                'description' => 'Monthly rent',
                'amount' => 15000.00,
                'type' => BankTransactionTypeEnum::WITHDRAWAL,
                'reference' => 'RENT-06',
                'user_id' => 1,
            ],
        ];

        foreach ($bankTransactions as $transactionData) {
            BankTransaction::create($transactionData);
        }

        // Create bank reconciliation
        $reconciliation = BankReconciliation::create([
            'bank_account_id' => 1,
            'statement_date' => Carbon::now()->subDays(1),
            'statement_balance' => 34778.75,
            'adjusted_balance' => 34778.75,
            'is_completed' => true,
            'notes' => 'June 2023 reconciliation',
        ]);

        // Mark transactions as reconciled
        BankTransaction::where('bank_account_id', 1)
            ->where('date', '<=', $reconciliation->statement_date)
            ->update(['reconciliation_id' => $reconciliation->id]);

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

        // Create journal entries for other transactions
        // Create journal entries that properly reference actual transactions
        $journalEntries = [
            // Journal entry for Order #1
            [
                'entry_date' => $orders[0]['created_at'],
                'reference_number' => 'ORDJ-001',
                'memo' => 'Revenue from Order #1',
                'user_id' => $orders[0]['user_id'],
                'referenceable_type' => Order::class,
                'referenceable_id' => 1,
                'journal_items' => [
                    [
                        'account_id' => Account::where('code', '4000')->first()->id, // Sales Revenue
                        'debit' => 0,
                        'credit' => 50.00,
                        'memo' => 'Revenue from sandwich sales'
                    ],
                    [
                        'account_id' => Account::where('code', '1121')->first()->id, // Main Bank Account
                        'debit' => 52.50,
                        'credit' => 0,
                        'memo' => 'Cash received'
                    ],
                    [
                        'account_id' => Account::where('code', '2400')->first()->id, // Sales Tax Payable
                        'debit' => 0,
                        'credit' => 2.50,
                        'memo' => 'Tax collected'
                    ]
                ]
            ],

            // Journal entry for Bill #1
            [
                'entry_date' => $bills[0]['date'],
                'reference_number' => 'BILLJ-001',
                'memo' => 'Inventory purchase from supplier',
                'user_id' => $bills[0]['user_id'],
                'referenceable_type' => Bill::class,
                'referenceable_id' => 1,
                'journal_items' => [
                    [
                        'account_id' => Account::where('code', '1300')->first()->id, // Inventory
                        'debit' => 1175.00,
                        'credit' => 0,
                        'memo' => 'Food supplies received'
                    ],
                    [
                        'account_id' => Account::where('code', '2100')->first()->id, // Accounts Payable
                        'debit' => 0,
                        'credit' => 1175.00,
                        'memo' => 'Owed to supplier'
                    ]
                ]
            ],

            // Journal entry for Bank Transaction (deposit)
            [
                'entry_date' => $bankTransactions[0]['date'],
                'reference_number' => 'BANKJ-001',
                'memo' => 'Customer payment received',
                'user_id' => $bankTransactions[0]['user_id'],
                'referenceable_type' => BankTransaction::class,
                'referenceable_id' => 1,
                'journal_items' => [
                    [
                        'account_id' => Account::where('code', '1121')->first()->id, // Main Bank Account
                        'debit' => 78.75,
                        'credit' => 0,
                        'memo' => 'Funds deposited'
                    ],
                    [
                        'account_id' => Account::where('code', '1200')->first()->id, // Accounts Receivable
                        'debit' => 0,
                        'credit' => 78.75,
                        'memo' => 'Customer payment applied'
                    ]
                ]
            ],

            // Journal entry for Inventory Transaction
            [
                'entry_date' => now(),
                'reference_number' => 'INVJ-001',
                'memo' => 'Initial inventory stock',
                'user_id' => $inventoryTransactions[0]['user_id'],
                'referenceable_type' => InventoryTransaction::class,
                'referenceable_id' => 1,
                'journal_items' => [
                    [
                        'account_id' => Account::where('code', '1300')->first()->id, // Inventory
                        'debit' => 750.00, // 50kg × 15.00/kg
                        'credit' => 0,
                        'memo' => 'Chicken breast received'
                    ],
                    [
                        'account_id' => Account::where('code', '1121')->first()->id, // Main Bank Account
                        'debit' => 0,
                        'credit' => 750.00,
                        'memo' => 'Payment for inventory'
                    ]
                ]
            ]
        ];

        foreach ($journalEntries as $entryData) {
            $journalEntry = JournalEntry::create([
                'entry_date' => $entryData['entry_date'],
                'reference_number' => $entryData['reference_number'],
                'memo' => $entryData['memo'],
                'user_id' => $entryData['user_id'],
                'referenceable_type' => $entryData['referenceable_type'],
                'referenceable_id' => $entryData['referenceable_id'],
            ]);

            foreach ($entryData['journal_items'] as $itemData) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $itemData['account_id'],
                    'debit' => $itemData['debit'],
                    'credit' => $itemData['credit'],
                    'memo' => $itemData['memo'],
                    'referenceable_type' => $entryData['referenceable_type'],
                    'referenceable_id' => $entryData['referenceable_id'],
                ]);
            }
        }
    }

}
