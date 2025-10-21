<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Complex query with multiple joins, subqueries, and raw SQL
        // This intentionally uses inefficient patterns to trigger slow query logging

        // Stats 1: Posts with engagement score using complex calculation
        $topEngagingPosts = DB::table('posts')
            ->select([
                'posts.id',
                'posts.title',
                DB::raw('posts.view_count'),
                DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
                DB::raw('COUNT(DISTINCT post_tag.tag_id) as tag_count'),
                DB::raw('(
                    posts.view_count * 0.1 +
                    COUNT(DISTINCT comments.id) * 5 +
                    COUNT(DISTINCT post_tag.tag_id) * 2 +
                    (SELECT COUNT(*) FROM comments c2 WHERE c2.post_id = posts.id AND c2.is_approved = 1) * 3
                ) as engagement_score')
            ])
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->leftJoin('post_tag', 'posts.id', '=', 'post_tag.post_id')
            ->where('posts.is_published', true)
            ->groupBy('posts.id', 'posts.title', 'posts.view_count')
            ->orderByRaw('engagement_score DESC')
            ->limit(10)
            ->get();

        // Stats 2: User activity with nested comment analysis
        $topActiveUsers = DB::table('users')
            ->select([
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT posts.id) as post_count'),
                DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments c
                    WHERE c.user_id = users.id
                    AND c.parent_id IS NOT NULL
                ) as reply_count'),
                DB::raw('(
                    SELECT COALESCE(SUM(p.view_count), 0)
                    FROM posts p
                    WHERE p.user_id = users.id
                ) as total_views'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments c
                    JOIN posts p ON c.post_id = p.id
                    WHERE p.user_id = users.id
                    AND c.user_id != users.id
                ) as received_comments')
            ])
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->leftJoin('comments', 'users.id', '=', 'comments.user_id')
            ->groupBy('users.id', 'users.name')
            ->having(DB::raw('post_count'), '>', 0)
            ->orderByRaw('(post_count * 10 + comment_count * 2 + total_views * 0.01) DESC')
            ->limit(10)
            ->get();

        // Stats 3: Category performance with complex aggregations
        $categoryStats = DB::table('categories')
            ->select([
                'categories.name',
                'categories.slug',
                DB::raw('COUNT(DISTINCT posts.id) as post_count'),
                DB::raw('COALESCE(AVG(posts.view_count), 0) as avg_views'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments
                    JOIN posts p2 ON comments.post_id = p2.id
                    WHERE p2.category_id = categories.id
                ) as total_comments'),
                DB::raw('(
                    SELECT COUNT(DISTINCT post_tag.tag_id)
                    FROM post_tag
                    JOIN posts p3 ON post_tag.post_id = p3.id
                    WHERE p3.category_id = categories.id
                ) as unique_tags'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM posts p4
                    WHERE p4.category_id = categories.id
                    AND p4.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) as recent_posts')
            ])
            ->leftJoin('posts', 'categories.id', '=', 'posts.category_id')
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByRaw('post_count DESC')
            ->get();

        // Stats 4: Tag cloud with weighted scoring
        $popularTags = DB::table('tags')
            ->select([
                'tags.name',
                DB::raw('COUNT(DISTINCT post_tag.post_id) as usage_count'),
                DB::raw('(
                    SELECT COALESCE(SUM(p.view_count), 0)
                    FROM post_tag pt2
                    JOIN posts p ON pt2.post_id = p.id
                    WHERE pt2.tag_id = tags.id
                ) as total_tag_views'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments c
                    JOIN post_tag pt3 ON c.post_id = pt3.post_id
                    WHERE pt3.tag_id = tags.id
                ) as comments_on_tagged_posts'),
                DB::raw('ROUND(
                    COUNT(DISTINCT post_tag.post_id) * 10 +
                    (
                        SELECT COALESCE(SUM(p.view_count), 0)
                        FROM post_tag pt2
                        JOIN posts p ON pt2.post_id = p.id
                        WHERE pt2.tag_id = tags.id
                    ) * 0.05
                ) as popularity_score')
            ])
            ->leftJoin('post_tag', 'tags.id', '=', 'post_tag.tag_id')
            ->groupBy('tags.id', 'tags.name')
            ->orderByRaw('popularity_score DESC')
            ->limit(15)
            ->get();

        // Stats 5: Time-based analysis with complex date calculations
        $monthlyStats = DB::select("
            SELECT
                DATE_FORMAT(posts.created_at, '%Y-%m') as month,
                COUNT(DISTINCT posts.id) as posts_created,
                COUNT(DISTINCT comments.id) as comments_created,
                COALESCE(SUM(posts.view_count), 0) as total_views,
                COUNT(DISTINCT posts.user_id) as active_authors,
                ROUND(AVG(
                    (SELECT COUNT(*) FROM comments c WHERE c.post_id = posts.id)
                ), 2) as avg_comments_per_post
            FROM posts
            LEFT JOIN comments ON posts.id = comments.post_id
            WHERE posts.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(posts.created_at, '%Y-%m')
            ORDER BY month DESC
        ");

        // Stats 6: Comment thread depth analysis (recursive-like query)
        $deepestThreads = DB::table('comments as c1')
            ->select([
                'c1.id',
                'c1.content',
                'posts.title as post_title',
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments c2
                    WHERE c2.parent_id = c1.id
                ) as direct_replies'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments c3
                    WHERE c3.parent_id IN (
                        SELECT id FROM comments WHERE parent_id = c1.id
                    )
                ) as nested_replies'),
                DB::raw('CHAR_LENGTH(c1.content) as content_length')
            ])
            ->join('posts', 'c1.post_id', '=', 'posts.id')
            ->whereNull('c1.parent_id')
            ->groupBy('c1.id', 'c1.content', 'posts.title')
            ->orderByRaw('direct_replies DESC')
            ->limit(10)
            ->get();

        // Overall stats with multiple subqueries
        $overallStats = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM posts) as total_posts,
                (SELECT COUNT(*) FROM posts WHERE is_published = 1) as published_posts,
                (SELECT COUNT(*) FROM comments) as total_comments,
                (SELECT COUNT(*) FROM comments WHERE is_approved = 1) as approved_comments,
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM categories) as total_categories,
                (SELECT COUNT(*) FROM tags) as total_tags,
                (SELECT COALESCE(SUM(view_count), 0) FROM posts) as total_views,
                (SELECT COALESCE(AVG(view_count), 0) FROM posts WHERE is_published = 1) as avg_views_per_post,
                (SELECT COUNT(DISTINCT user_id) FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as active_authors_week
        ");

        return view('dashboard', compact(
            'topEngagingPosts',
            'topActiveUsers',
            'categoryStats',
            'popularTags',
            'monthlyStats',
            'deepestThreads',
            'overallStats'
        ));
    }
}
