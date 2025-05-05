<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // Enable foreign key constraints
        Schema::enableForeignKeyConstraints();

        // 1. Core System Tables

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->foreignId('parent_account_id')->nullable()->constrained('accounts');
            $table->boolean('is_system_account')->default(false);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['code', 'type']);
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('rate', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['name', 'is_active']);
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('legal_name', 100)->nullable(); // For official documents
            $table->string('tax_id', 50)->nullable(); // Restaurant's own tax ID
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->text('address');
            $table->string('city', 50);
            $table->string('country', 50)->default('United Arab Emirates');
            $table->string('timezone', 50)->default('UTC');
            $table->string('currency', 3)->default('AED');
            $table->string('logo_path', 255)->nullable();
            $table->json('business_hours')->nullable(); // Store as JSON {day: {open, close}}
            $table->foreignId('default_tax_rate_id')->nullable()->constrained('tax_rates');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained();
            $table->string('position', 50);
            $table->decimal('salary', 8, 2)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->string('passport_expiry_date', 50)->nullable();
            $table->string('emirates_id_number', 50)->nullable();
            $table->string('emirates_id_expiry_date', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['position', 'hire_date', 'emirates_id_expiry_date']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Restaurant Operations Tables
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Menu::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignIdFor(\App\Models\Account::class, 'account_id')->nullable()->constrained()->onDelete('cascade');;
            $table->timestamps();
            $table->index(['name', 'is_active']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('menu_categories');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('cost', 8, 2)->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_available')->default(true);
            $table->string('image', 255)->nullable();
            $table->json('options')->nullable();
            $table->foreignId('tax_rate_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Account::class, 'account_id')->nullable()->constrained()->onDelete('cascade');;
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'price', 'is_available']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->foreignIdFor(\App\Models\Account::class, 'account_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['name', 'phone']);
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('unit', 20);
            $table->decimal('current_quantity', 10, 3)->default(0);
            $table->decimal('alert_quantity', 10, 3)->nullable();
            $table->timestamps();
            $table->index(['name', 'current_quantity']);
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->string('type');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->foreignId('user_id')->constrained();
            $table->text('notes')->nullable();
            $table->nullableMorphs('referenceable');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['type', 'created_at']);
        });

        // 3. POS Operations Tables
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained();
            $table->string('name', 50);
            $table->integer('capacity');
            $table->string('status', 20)->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['name', 'status']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->foreignIdFor(\App\Models\Account::class, 'account_id')->nullable()->constrained()->onDelete('cascade');;
            $table->timestamps();
            $table->index(['name', 'phone']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Account::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\BankAccount::class)->nullable()->constrained();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->useCurrent();
            $table->string('payment_method', 50);
            $table->string('reference_number', 50)->nullable();
            $table->foreignId('user_id')->constrained();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['payment_date', 'payment_method']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('staff_id')->nullable()->constrained('staff');
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->foreignIdFor(\App\Models\Payment::class)->nullable()->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained();
            $table->decimal('unit_price', 8, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->json('selected_options')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'menu_item_id']);
        });

        // 4. Accounting System Tables


        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('reference_number', 50)->unique();
            $table->text('memo')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->nullableMorphs('referenceable');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['entry_date', 'reference_number']);
        });

        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained();
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->text('memo')->nullable();
            $table->morphs('referenceable');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['account_id', 'journal_entry_id']);
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->date('date');
            $table->date('due_date');
            $table->foreignIdFor(\App\Models\Payment::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Supplier::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');;
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignIdFor(\App\Models\Bill::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Payment::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['account_id', 'date']);
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('routing_number', 50)->nullable();
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index(['account_number', 'bank_name']);
        });

        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained();
            $table->date('statement_date');
            $table->decimal('statement_balance', 12, 2);
            $table->decimal('adjusted_balance', 12, 2);
            $table->boolean('is_completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['bank_account_id', 'statement_date']);
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->foreignId('reconciliation_id')->nullable()->constrained('bank_reconciliations');
            $table->foreignId('user_id')->constrained();
            $table->foreignIdFor(\App\Models\Account::class)->nullable()->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('type');
            $table->string('reference_number', 50)->nullable();
            $table->timestamps();
            $table->index(['bank_account_id', 'date', 'type']);
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['staff_id', 'start_time']);
        });

        Schema::create('setups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->foreignIdFor(\App\Models\Account::class, 'account_id')->nullable()->constrained()->onDelete('cascade');;
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Disable foreign key constraints during drop
        Schema::disableForeignKeyConstraints();

        // Drop tables in reverse order to avoid foreign key conflicts
        $tables = [
            'accounts_setups',
            'shifts',
            'tax_rates',
            'bank_transactions',
            'bank_reconciliations',
            'bank_accounts',
            'payments',
            'expenses',
            'bills',
            'journal_items',
            'journal_entries',
            'accounts',
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
            'restaurants',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }
};
