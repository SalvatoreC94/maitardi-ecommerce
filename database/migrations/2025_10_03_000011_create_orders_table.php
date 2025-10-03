<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // es. ORD-20251003-0001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', ['pending','paid','processing','shipped','completed','cancelled','refunded'])->default('pending');

            // Totali
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Pagamenti
            $table->string('payment_method')->nullable(); // 'stripe','cod','test'
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');

            // Dati cliente snapshot
            $table->string('customer_email');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();

            $table->json('billing_address_json')->nullable();
            $table->json('shipping_address_json')->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
