<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('rides', 'preference_charge')) {
            Schema::table('rides', function (Blueprint $table) {
                $table->double('preference_charge')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rides', 'preference_charge')) {
            Schema::table('rides', function (Blueprint $table) {
                $table->dropColumn('preference_charge');
            });
        }
    }
};
