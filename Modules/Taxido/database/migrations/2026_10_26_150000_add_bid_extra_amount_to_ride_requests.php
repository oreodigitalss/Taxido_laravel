<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ride_requests', 'bid_extra_amount')) {
            Schema::table('ride_requests', function (Blueprint $table) {
                $table->double('bid_extra_amount')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ride_requests', 'bid_extra_amount')) {
            Schema::table('ride_requests', function (Blueprint $table) {
                $table->dropColumn('bid_extra_amount');
            });
        }
    }
};
