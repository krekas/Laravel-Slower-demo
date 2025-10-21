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
        // Query 1: Overall stats - simplest query
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
                (SELECT COALESCE(AVG(view_count), 0) FROM posts WHERE is_published = 1) as avg_views_per_post
        ");

        // Query 2: Top engaging posts with N+1 pattern (intentionally inefficient)
        $topEngagingPosts = DB::table('posts')
            ->select([
                'posts.id',
                'posts.title',
                'posts.view_count',
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count'),
                DB::raw('(SELECT COUNT(*) FROM post_tag WHERE post_tag.post_id = posts.id) as tag_count'),
                DB::raw('(posts.view_count * 0.1 +
                         (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) * 5 +
                         (SELECT COUNT(*) FROM post_tag WHERE post_tag.post_id = posts.id) * 2) as engagement_score')
            ])
            ->where('posts.is_published', true)
            ->orderByRaw('engagement_score DESC')
            ->limit(10)
            ->get();

        // Query 3: Top active users with multiple subqueries
        $topActiveUsers = DB::table('users')
            ->select([
                'users.id',
                'users.name',
                DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id) as post_count'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id) as comment_count'),
                DB::raw('(SELECT COALESCE(SUM(view_count), 0) FROM posts WHERE posts.user_id = users.id) as total_views')
            ])
            ->havingRaw('post_count > 0')
            ->orderByRaw('(post_count * 10 + comment_count * 2) DESC')
            ->limit(10)
            ->get();

        // Query 4: Category stats with joins
        $categoryStats = DB::table('categories')
            ->select([
                'categories.name',
                'categories.slug',
                DB::raw('COUNT(DISTINCT posts.id) as post_count'),
                DB::raw('COALESCE(AVG(posts.view_count), 0) as avg_views'),
                DB::raw('(SELECT COUNT(*) FROM comments
                         JOIN posts p2 ON comments.post_id = p2.id
                         WHERE p2.category_id = categories.id) as total_comments')
            ])
            ->leftJoin('posts', 'categories.id', '=', 'posts.category_id')
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByRaw('post_count DESC')
            ->get();

        // Query 5: Popular tags with correlated subquery
        $popularTags = DB::table('tags')
            ->select([
                'tags.name',
                DB::raw('(SELECT COUNT(*) FROM post_tag WHERE post_tag.tag_id = tags.id) as usage_count'),
                DB::raw('(SELECT COALESCE(SUM(p.view_count), 0)
                         FROM post_tag pt
                         JOIN posts p ON pt.post_id = p.id
                         WHERE pt.tag_id = tags.id) as total_tag_views')
            ])
            ->orderByRaw('usage_count DESC')
            ->limit(15)
            ->get();

        // Query 6: Monthly stats with date grouping
        $monthlyStats = DB::table('posts')
            ->select([
                DB::raw('DATE_FORMAT(posts.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT posts.id) as posts_created'),
                DB::raw('(SELECT COUNT(*) FROM comments
                         WHERE DATE_FORMAT(comments.created_at, "%Y-%m") = DATE_FORMAT(posts.created_at, "%Y-%m")) as comments_created'),
                DB::raw('COALESCE(SUM(posts.view_count), 0) as total_views')
            ])
            ->where('posts.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 12 MONTH)'))
            ->groupBy(DB::raw('DATE_FORMAT(posts.created_at, "%Y-%m")'))
            ->orderBy('month', 'DESC')
            ->get();

        // Query 7: Top commented posts
        $topCommentedPosts = DB::table('posts')
            ->select([
                'posts.id',
                'posts.title',
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND comments.is_approved = 1) as approved_comment_count')
            ])
            ->orderByRaw('comment_count DESC')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'overallStats',
            'topEngagingPosts',
            'topActiveUsers',
            'categoryStats',
            'popularTags',
            'monthlyStats',
            'topCommentedPosts'
        ));
    }
}
