<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('invitation_settings', 'event_name')) {
                $table->string('event_name')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invitation_settings', function (Blueprint $table) {
            if (Schema::hasColumn('invitation_settings', 'event_name')) {
                $table->dropColumn('event_name');
            }
        });
    }
};
