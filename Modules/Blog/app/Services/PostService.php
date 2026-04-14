<?php

namespace Modules\Blog\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Str;
use Modules\Blog\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    use HandlesIndexQuery;

    /**
     * Find a post by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): Post
    {
        $query = Post::query();
        if ($withTrashed) {
            $query->withTrashed();
        }
        return $query->findOrFail($id);
    }

    /**
     * Get list of posts with filters.
     */
    public function getPosts(array $params)
    {
        $query = Post::query()->with(['category', 'author']);

        return $this->handleIndexQuery(
            $query,
            $params,
            ['title', 'slug', 'summary', 'content'],
            fn($q) => $q->when($params['category_id'] ?? null, fn($subQ, $catId) => $subQ->where('category_id', $catId))
                       ->when($params['is_featured'] ?? null, fn($subQ, $isFeatured) => $subQ->where('is_featured', $isFeatured))
                       ->when($params['status'] ?? null, fn($subQ, $status) => $subQ->where('status', $status))
        );
    }

    /**
     * Create a new post.
     */
    public function createPost(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            if (($data['status'] ?? 'draft') === 'published' && !isset($data['published_at'])) {
                $data['published_at'] = now();
            }

            $post = Post::create($data);

            if (isset($data['featured_image'])) {
                $post->addMedia($data['featured_image'])
                    ->toMediaCollection('featured_image');
            }

            return $post;
        });
    }

    /**
     * Update a post.
     */
    public function updatePost(Post $post, array $data)
    {
        return DB::transaction(function () use ($post, $data) {
            if (isset($data['title']) && $data['title'] !== $post->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $post->id);
            }

            if (isset($data['status']) && $data['status'] === 'published' && !$post->published_at) {
                $data['published_at'] = now();
            }

            $post->update($data);

            if (isset($data['featured_image'])) {
                $post->clearMediaCollection('featured_image');
                $post->addMedia($data['featured_image'])
                    ->toMediaCollection('featured_image');
            }

            return $post;
        });
    }

    /**
     * Delete a post.
     */
    public function deletePost(Post $post)
    {
        return $post->delete();
    }

    /**
     * Restore a post.
     */
    public function restore(string $id): Post
    {
        return DB::transaction(function () use ($id) {
            $post = Post::onlyTrashed()->findOrFail($id);
            $post->restore();
            return $post->refresh();
        });
    }

    /**
     * Force delete a post.
     */
    public function forceDelete(string $id): Post
    {
        return DB::transaction(function () use ($id) {
            $post = Post::onlyTrashed()->findOrFail($id);
            $postData = clone $post;
            $post->forceDelete();
            return $postData;
        });
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete' => Post::query(),
                'restore',
                'forceDelete' => Post::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $posts = $query->whereIn('id', $ids)->get();
            $foundIds = $posts->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($posts->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        Post::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        Post::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        Post::onlyTrashed()->whereIn('id', $foundIds)->forceDelete();
                        break;
                }

                if ($operation !== 'forceDelete') {
                    // Refetch models to get their current state (especially for restore)
                    $posts = Post::withTrashed()->with(['category', 'author'])->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $posts,
                'failed_ids' => $notFoundIds,
            ];
        });
    }

    /**
     * Generate a unique slug for the post.
     */
    protected function generateUniqueSlug(string $title, $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
