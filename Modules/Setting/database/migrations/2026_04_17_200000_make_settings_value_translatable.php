<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename existing columns
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('value', 'value_old');
            $table->renameColumn('description', 'description_old');
        });

        // 2. Add new JSON columns
        Schema::table('settings', function (Blueprint $table) {
            $table->json('value')->nullable();
            $table->json('description')->nullable();
        });

        // 3. Migrate data
        $settings = DB::table('settings')->get();
        foreach ($settings as $setting) {
            DB::table('settings')->where('id', $setting->id)->update([
                'value' => json_encode(['en' => $setting->value_old]),
                'description' => json_encode(['en' => $setting->description_old]),
            ]);
        }

        // 4. Drop old columns
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['value_old', 'description_old']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('value', 'value_old');
            $table->renameColumn('description', 'description_old');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->text('value')->nullable();
            $table->text('description')->nullable();
        });

        // Data migration back (en only)
        $settings = DB::table('settings')->get();
        foreach ($settings as $setting) {
            $value = json_decode($setting->value_old, true);
            $desc = json_decode($setting->description_old, true);
            DB::table('settings')->where('id', $setting->id)->update([
                'value' => $value['en'] ?? null,
                'description' => $desc['en'] ?? null,
            ]);
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['value_old', 'description_old']);
        });
    }
};
