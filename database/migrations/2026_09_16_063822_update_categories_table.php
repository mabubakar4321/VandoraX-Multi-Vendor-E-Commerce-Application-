
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon')->nullable();
            $table->decimal('commission_rate', 5, 2)
                ->default(10.00);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'icon',
                'commission_rate',
                'created_by',
                'deleted_at',
            ]);
        });
    }
};