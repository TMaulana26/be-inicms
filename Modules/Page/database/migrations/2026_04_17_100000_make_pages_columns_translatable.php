<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('pages', 'title')) {
            return;
        }

        // 1. Rename existing columns
        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('title', 'title_old');
            $table->renameColumn('content', 'content_old');
        });

        // 2. Add new JSON columns
        Schema::table('pages', function (Blueprint $table) {
            $table->json('title')->nullable();
            $table->json('content')->nullable();
        });

        // 3. Migrate data
        $pages = DB::table('pages')->get();
        foreach ($pages as $page) {
            DB::table('pages')->where('id', $page->id)->update([
                'title' => json_encode(['en' => $page->title_old]),
                'content' => json_encode(['en' => $page->content_old]),
            ]);
        }

        // 4. Drop old columns
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title_old', 'content_old']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert logic...
    }
};
