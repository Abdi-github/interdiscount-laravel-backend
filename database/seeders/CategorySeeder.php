<?php

namespace Database\Seeders;

use App\Domain\Category\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = json_decode(file_get_contents(base_path('data/categories.json')), true);

        // Build mongo_id -> category_id (string) map for parent resolution
        $mongoToCategoryId = [];
        foreach ($categories as $cat) {
            $mongoToCategoryId[$cat['_id']] = $cat['category_id'];
        }

        // Deduplicate slugs by appending category_id on collision
        $seenSlugs = [];

        // First pass: create all categories without parent_id
        foreach ($categories as $cat) {
            $slug = $cat['slug'];
            if (isset($seenSlugs[$slug])) {
                $slug = $slug . '-' . $cat['category_id'];
            }
            $seenSlugs[$slug] = true;

            Category::create([
                'name' => $cat['name'],
                'slug' => $slug,
                'category_id' => $cat['category_id'],
                'level' => $cat['level'] ?? 1,
                'parent_id' => null, // Set in second pass
                'sort_order' => $cat['sort_order'] ?? 0,
                'is_active' => $cat['is_active'] ?? true,
            ]);
        }

        // Build category_id (string) -> mysql_id map
        $catIdMap = Category::all()->pluck('id', 'category_id')->toArray();

        // Second pass: set parent_id
        foreach ($categories as $cat) {
            if (!empty($cat['parent_id'])) {
                $parentCategoryId = $mongoToCategoryId[$cat['parent_id']] ?? null;
                if ($parentCategoryId && isset($catIdMap[$parentCategoryId])) {
                    Category::where('category_id', $cat['category_id'])
                        ->update(['parent_id' => $catIdMap[$parentCategoryId]]);
                }
            }
        }
    }
}
