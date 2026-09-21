<?php

use App\Models\Guest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Guest::query()->each(function (Guest $guest) {
            $base = Str::slug($guest->name) ?: 'tamu';
            $slug = $base;
            $counter = 2;

            while (Guest::where('slug', $slug)->whereKeyNot($guest->id)->exists()) {
                $slug = $base . '-' . $counter++;
            }

            $guest->updateQuietly(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};