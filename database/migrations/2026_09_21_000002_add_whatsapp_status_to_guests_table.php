<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'whatsapp_status')) {
                $table->enum('whatsapp_status', ['pending', 'queued', 'sending', 'sent', 'failed'])
                    ->default('pending')
                    ->after('rsvp_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (Schema::hasColumn('guests', 'whatsapp_status')) {
                $table->dropColumn('whatsapp_status');
            }
        });
    }
};
