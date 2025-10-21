<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $users[] = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        // Create categories
        $categories = [];
        $categoryNames = ['Technology', 'Business', 'Health', 'Science', 'Sports', 'Entertainment', 'Travel', 'Food'];
        foreach ($categoryNames as $name) {
            $categories[] = Category::create([
                'name' => $name,
                'slug' => strtolower($name),
                'description' => "All about $name",
            ]);
        }

        // Create tags
        $tags = [];
        $tagNames = ['PHP', 'Laravel', 'MySQL', 'Performance', 'Tutorial', 'News', 'Tips', 'Guide', 'Review', 'Opinion'];
        foreach ($tagNames as $name) {
            $tags[] = Tag::create([
                'name' => $name,
                'slug' => strtolower($name),
            ]);
        }

        // Create posts with relationships
        for ($i = 1; $i <= 100; $i++) {
            $post = Post::create([
                'title' => "Blog Post $i - " . fake()->sentence(4),
                'slug' => "blog-post-$i",
                'content' => fake()->paragraphs(5, true),
                'excerpt' => fake()->sentence(15),
                'user_id' => $users[array_rand($users)]->id,
                'category_id' => $categories[array_rand($categories)]->id,
                'is_published' => rand(0, 100) > 20, // 80% published
                'view_count' => rand(0, 10000),
                'published_at' => rand(0, 100) > 20 ? now()->subDays(rand(0, 365)) : null,
            ]);

            // Attach random tags to post (1-5 tags)
            $randomTags = array_rand($tags, rand(1, min(5, count($tags))));
            if (!is_array($randomTags)) {
                $randomTags = [$randomTags];
            }
            foreach ($randomTags as $tagIndex) {
                $post->tags()->attach($tags[$tagIndex]->id);
            }

            // Create comments for post
            $commentCount = rand(0, 15);
            for ($j = 1; $j <= $commentCount; $j++) {
                $comment = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $users[array_rand($users)]->id,
                    'content' => fake()->paragraph(),
                    'is_approved' => rand(0, 100) > 10, // 90% approved
                ]);

                // Add some replies (30% chance)
                if (rand(0, 100) < 30) {
                    $replyCount = rand(1, 3);
                    for ($k = 1; $k <= $replyCount; $k++) {
                        Comment::create([
                            'post_id' => $post->id,
                            'user_id' => $users[array_rand($users)]->id,
                            'parent_id' => $comment->id,
                            'content' => fake()->paragraph(),
                            'is_approved' => rand(0, 100) > 10,
                        ]);
                    }
                }
            }
        }
    }
}
