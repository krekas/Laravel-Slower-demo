<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LargeDatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a large dataset to ensure queries are slow enough to be caught by Laravel Slower
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        Comment::truncate();
        DB::table('post_tag')->truncate();
        Post::truncate();
        Category::truncate();
        Tag::truncate();
        // Keep existing users but add more

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Creating users...');

        // Get existing users or create some
        $existingUsers = User::all();
        if ($existingUsers->count() === 0) {
            $users = User::factory(50)->create();
        } else {
            // Add more users to existing ones
            $newUsers = User::factory(100)->create();
            $users = $existingUsers->concat($newUsers);
        }

        $this->command->info('Creating categories...');
        $categories = [];
        $categoryNames = [
            'Technology', 'Science', 'Business', 'Health', 'Entertainment',
            'Sports', 'Politics', 'Education', 'Travel', 'Food',
            'Fashion', 'Art', 'Music', 'Movies', 'Books',
            'Gaming', 'Photography', 'Fitness', 'Lifestyle', 'Finance'
        ];

        foreach ($categoryNames as $name) {
            $categories[] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "All about {$name} and related topics"
            ]);
        }

        $this->command->info('Creating tags...');
        $tags = [];
        $tagNames = [
            'PHP', 'Laravel', 'JavaScript', 'Python', 'Java', 'C++', 'Ruby', 'Go',
            'Vue', 'React', 'Angular', 'Node.js', 'Docker', 'Kubernetes', 'AWS',
            'Tutorial', 'Guide', 'News', 'Review', 'Opinion', 'Analysis', 'Research',
            'Beginner', 'Advanced', 'Expert', 'Tips', 'Tricks', 'Best Practices',
            'Security', 'Performance', 'Optimization', 'Testing', 'Debugging',
            'Design', 'Architecture', 'Patterns', 'Database', 'API', 'REST',
            'GraphQL', 'Frontend', 'Backend', 'FullStack', 'DevOps', 'ML',
            'AI', 'Blockchain', 'IoT', 'Mobile', 'Web', 'Desktop'
        ];

        foreach ($tagNames as $name) {
            $tags[] = Tag::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }

        $this->command->info('Creating posts (this will take a while)...');
        $posts = [];
        $batchSize = 500;
        $totalPosts = 5000; // Create 5000 posts

        for ($i = 0; $i < $totalPosts; $i++) {
            $category = $categories[array_rand($categories)];
            $user = $users->random();

            // Create posts with varying dates over the past year
            $createdAt = now()->subDays(rand(1, 365));

            $posts[] = [
                'title' => $this->generateTitle(),
                'slug' => Str::slug($this->generateTitle()) . '-' . $i,
                'content' => $this->generateContent(),
                'excerpt' => $this->generateExcerpt(),
                'user_id' => $user->id,
                'category_id' => $category->id,
                'is_published' => rand(0, 100) > 10, // 90% published
                'view_count' => rand(0, 10000),
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Insert in batches
            if (count($posts) >= $batchSize) {
                Post::insert($posts);
                $this->command->info("Created {$i} posts...");
                $posts = [];
            }
        }

        // Insert remaining posts
        if (count($posts) > 0) {
            Post::insert($posts);
        }

        $this->command->info('Attaching tags to posts...');
        $allPosts = Post::all();
        $tagAttachments = [];

        foreach ($allPosts as $post) {
            // Attach 1-8 random tags to each post
            $postTags = collect($tags)->random(rand(1, 8));
            foreach ($postTags as $tag) {
                $tagAttachments[] = [
                    'post_id' => $post->id,
                    'tag_id' => $tag->id,
                ];
            }

            // Insert in batches to avoid memory issues
            if (count($tagAttachments) >= 1000) {
                DB::table('post_tag')->insert($tagAttachments);
                $tagAttachments = [];
            }
        }

        if (count($tagAttachments) > 0) {
            DB::table('post_tag')->insert($tagAttachments);
        }

        $this->command->info('Creating comments (this will take a long while)...');
        $comments = [];
        $totalComments = 15000; // Create 15000 comments

        for ($i = 0; $i < $totalComments; $i++) {
            $post = $allPosts->random();
            $user = $users->random();

            $comments[] = [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => null, // First create parent comments
                'content' => $this->generateCommentContent(),
                'is_approved' => rand(0, 100) > 5, // 95% approved
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];

            if (count($comments) >= $batchSize) {
                Comment::insert($comments);
                $this->command->info("Created {$i} comments...");
                $comments = [];
            }
        }

        if (count($comments) > 0) {
            Comment::insert($comments);
        }

        $this->command->info('Creating nested comments (replies)...');
        $allComments = Comment::all();
        $replies = [];
        $totalReplies = 10000; // Create 10000 reply comments

        for ($i = 0; $i < $totalReplies; $i++) {
            $parentComment = $allComments->random();
            $user = $users->random();

            $replies[] = [
                'post_id' => $parentComment->post_id,
                'user_id' => $user->id,
                'parent_id' => $parentComment->id,
                'content' => $this->generateCommentContent(),
                'is_approved' => rand(0, 100) > 5,
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];

            if (count($replies) >= $batchSize) {
                Comment::insert($replies);
                $this->command->info("Created {$i} reply comments...");
                $replies = [];
            }
        }

        if (count($replies) > 0) {
            Comment::insert($replies);
        }

        $this->command->info('Large dataset seeding completed!');
        $this->command->info('Total Posts: ' . Post::count());
        $this->command->info('Total Comments: ' . Comment::count());
        $this->command->info('Total Tags: ' . Tag::count());
        $this->command->info('Total Categories: ' . Category::count());
        $this->command->info('Total Users: ' . User::count());
    }

    private function generateTitle(): string
    {
        $templates = [
            'How to Master %s in %d Days',
            'The Ultimate Guide to %s',
            'Top %d Tips for %s',
            'Understanding %s: A Complete Guide',
            '%s Best Practices for %d',
            'Why %s Matters in Modern Development',
            'Getting Started with %s',
            'Advanced %s Techniques',
            'Common Mistakes in %s and How to Avoid Them',
            'The Future of %s',
        ];

        $topics = [
            'Laravel', 'PHP', 'JavaScript', 'Python', 'Web Development',
            'API Design', 'Database Optimization', 'Clean Code', 'Testing',
            'Security', 'Performance', 'Cloud Computing', 'DevOps'
        ];

        $template = $templates[array_rand($templates)];
        $topic = $topics[array_rand($topics)];

        return sprintf($template, $topic, rand(3, 30));
    }

    private function generateContent(): string
    {
        $paragraphs = rand(5, 15);
        $content = [];

        for ($i = 0; $i < $paragraphs; $i++) {
            $sentences = rand(3, 8);
            $paragraph = [];

            for ($j = 0; $j < $sentences; $j++) {
                $paragraph[] = $this->generateSentence();
            }

            $content[] = implode(' ', $paragraph);
        }

        return implode("\n\n", $content);
    }

    private function generateExcerpt(): string
    {
        return $this->generateSentence() . ' ' . $this->generateSentence();
    }

    private function generateCommentContent(): string
    {
        $sentences = rand(1, 5);
        $comment = [];

        for ($i = 0; $i < $sentences; $i++) {
            $comment[] = $this->generateSentence();
        }

        return implode(' ', $comment);
    }

    private function generateSentence(): string
    {
        $starters = [
            'This is a great', 'I think that', 'In my experience',
            'The best way to', 'You should always', 'Never forget to',
            'It is important to', 'Many developers', 'The key to',
            'When working with', 'One of the most', 'This approach',
        ];

        $middles = [
            'approach for handling', 'solution to', 'method of implementing',
            'way to optimize', 'technique for managing', 'strategy for building',
            'pattern for designing', 'practice for maintaining', 'tool for debugging',
        ];

        $endings = [
            'complex applications.', 'scalable systems.', 'modern web apps.',
            'production environments.', 'large codebases.', 'real-world projects.',
            'enterprise solutions.', 'distributed systems.', 'microservices.',
        ];

        return $starters[array_rand($starters)] . ' ' .
               $middles[array_rand($middles)] . ' ' .
               $endings[array_rand($endings)];
    }
}
