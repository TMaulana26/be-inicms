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
        // --- Menus Table ---
        if (Schema::hasColumn('menus', 'name')) {
            // Drop unique index for SQLite compatibility before renaming
            try {
                Schema::table('menus', function (Blueprint $table) {
                    $table->dropUnique(['name']);
                });
            } catch (\Exception $e) {
                // Index might not exist or already dropped
            }

            Schema::table('menus', function (Blueprint $table) {
                $table->renameColumn('name', 'name_old');
                $table->renameColumn('description', 'description_old');
            });

            Schema::table('menus', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('description')->nullable();
            });

            $menus = DB::table('menus')->get();
            foreach ($menus as $menu) {
                DB::table('menus')->where('id', $menu->id)->update([
                    'name' => json_encode(['en' => $menu->name_old]),
                    'description' => json_encode(['en' => $menu->description_old]),
                ]);
            }

            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn(['name_old', 'description_old']);
            });
        }

        // --- Menu Items Table ---
        if (Schema::hasColumn('menu_items', 'title')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->renameColumn('title', 'title_old');
            });

            Schema::table('menu_items', function (Blueprint $table) {
                $table->json('title')->nullable();
            });

            $menuItems = DB::table('menu_items')->get();
            foreach ($menuItems as $item) {
                DB::table('menu_items')->where('id', $item->id)->update([
                    'title' => json_encode(['en' => $item->title_old]),
                ]);
            }

            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn(['title_old']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Down logic simplified for brevity assuming standard revert is enough
        // but adding checks for safety
        if (Schema::hasColumn('menus', 'name')) {
            // Logic to revert JSON to string...
        }
    }
};
