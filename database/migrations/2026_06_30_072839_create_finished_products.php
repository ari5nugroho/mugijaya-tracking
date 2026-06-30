<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finished_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name'); // contoh: "Kusen Aluminium", "Pintu Kaca Frameless"
            // Harga dasar per m2, dipakai sebagai default saat membuat order_item.
            // Bisa di-override manual per order kalau ada negosiasi/diskon.
            $table->decimal('price_per_m2', 15, 2)->default(0);
            // Estimasi pemakaian bahan baku per m2 (opsional, untuk kalkulasi kebutuhan stok)
            $table->text('material_notes')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finished_products');
    }
};
