<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Modify column to be nullable
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            // Re-add constraint with onDelete set null
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            
            // Add custom name column for manual entry items
            $table->string('custom_name')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->dropColumn('custom_name');
        });
    }
};
