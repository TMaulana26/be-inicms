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
        // First, drop the menu_items table as it's being merged into menus
        Schema::dropIfExists('menu_items');

        // Modify menus table
        Schema::table('menus', function (Blueprint $table) {
            // Check if columns exist before adding (in case of re-run or partial migrations)
            if (!Schema::hasColumn('menus', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('menus')->onDelete('cascade');
            }
            if (!Schema::hasColumn('menus', 'title')) {
                $table->string('title')->after('parent_id')->nullable();
            }
            if (!Schema::hasColumn('menus', 'icon')) {
                $table->string('icon')->after('title')->nullable();
            }
            if (!Schema::hasColumn('menus', 'url')) {
                $table->string('url')->after('icon')->nullable();
            }
            if (!Schema::hasColumn('menus', 'target')) {
                $table->string('target')->after('url')->default('_self');
            }
            if (!Schema::hasColumn('menus', 'order')) {
                $table->integer('order')->after('target')->default(0);
            }
            
            // name and slug are already there, but we might want to make name nullable 
            // since items might only have title
            $table->string('name')->nullable()->change();
            $table->string('slug')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse, we'd need to recreate menu_items and restore menus structure
        // This is complex, but for now we'll just drop the added columns
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'title', 'icon', 'url', 'target', 'order']);
            $table->string('name')->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
