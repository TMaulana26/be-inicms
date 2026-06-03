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
        // --- Posts Table ---
        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('title', 'title_old');
            $table->renameColumn('summary', 'summary_old');
            $table->renameColumn('content', 'content_old');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->json('title')->nullable();
            $table->json('summary')->nullable();
            $table->json('content')->nullable();
        });

        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            DB::table('posts')->where('id', $post->id)->update([
                'title' => json_encode(['en' => $post->title_old]),
                'summary' => json_encode(['en' => $post->summary_old]),
                'content' => json_encode(['en' => $post->content_old]),
            ]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title_old', 'summary_old', 'content_old']);
        });

        // --- Categories Table ---
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'name_old');
            $table->renameColumn('description', 'description_old');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->json('name')->nullable();
            $table->json('description')->nullable();
        });

        $categories = DB::table('categories')->get();
        foreach ($categories as $category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => json_encode(['en' => $category->name_old]),
                'description' => json_encode(['en' => $category->description_old]),
            ]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_old', 'description_old']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse for posts
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title_new')->nullable();
            $table->text('summary_new')->nullable();
            $table->longText('content_new')->nullable();
        });

        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            $title = json_decode($post->title, true);
            $summary = json_decode($post->summary, true);
            $content = json_decode($post->content, true);
            DB::table('posts')->where('id', $post->id)->update([
                'title_new' => $title['en'] ?? '',
                'summary_new' => $summary['en'] ?? '',
                'content_new' => $content['en'] ?? '',
            ]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title', 'summary', 'content']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('title_new', 'title');
            $table->renameColumn('summary_new', 'summary');
            $table->renameColumn('content_new', 'content');
        });

        // Reverse for categories
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_new')->nullable();
            $table->text('description_new')->nullable();
        });

        $categories = DB::table('categories')->get();
        foreach ($categories as $category) {
            $name = json_decode($category->name, true);
            $desc = json_decode($category->description, true);
            DB::table('categories')->where('id', $category->id)->update([
                'name_new' => $name['en'] ?? '',
                'description_new' => $desc['en'] ?? '',
            ]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_new', 'name');
            $table->renameColumn('description_new', 'description');
        });
    }
};
