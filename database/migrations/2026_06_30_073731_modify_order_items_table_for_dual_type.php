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
        Schema::table('order_items', function (Blueprint $table) {
            // Jenis item: bahan baku standar atau fabrikasi custom
            $table
                ->enum('item_type', ['raw_material', 'finished_product'])
                ->default('raw_material')
                ->after('order_id');

            // product_id jadi nullable karena item bisa berupa finished_product
            $table->foreignId('product_id')->nullable()->change();

            $table->foreignId('finished_product_id')->nullable()->after('product_id')->constrained('finished_products')->nullOnDelete();

            // Dimensi custom (dalam meter), hanya relevan untuk finished_product
            $table->decimal('custom_width', 8, 2)->nullable()->after('quantity_delivered');
            $table->decimal('custom_height', 8, 2)->nullable()->after('custom_width');
            $table->decimal('custom_area', 10, 4)->nullable()->after('custom_height'); // m2, dihitung otomatis

            // Harga & subtotal disimpan di order_item supaya histori harga tidak berubah
            // walau harga master product/finished_product berubah di kemudian hari
            $table->decimal('price_per_unit', 15, 2)->default(0)->after('custom_area');
            $table->decimal('subtotal', 15, 2)->default(0)->after('price_per_unit');

            // quantity_ordered jadi nullable karena finished_product dihitung dari area, bukan qty pcs
            $table->integer('quantity_ordered')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finished_product_id');
            $table->dropColumn(['item_type', 'custom_width', 'custom_height', 'custom_area', 'price_per_unit', 'subtotal']);
            $table->foreignId('product_id')->nullable(false)->change();
            $table->integer('quantity_ordered')->nullable(false)->change();
        });
    }
};
