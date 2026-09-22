<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `vendor_documents` MODIFY COLUMN `document_type`
            ENUM('cnic_front','cnic_back','tax_certificate','bank_proof','store_photo') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `vendor_documents` MODIFY COLUMN `document_type`
            ENUM('cnic','tax_certificate','bank_proof','store_photo') NOT NULL");
    }
};