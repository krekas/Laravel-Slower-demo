# Laravel Slower Demo Project

This is a demo project created to test the [Laravel Slower](https://github.com/halilcosdu/laravel-slower) package.

## What's Included

This project contains intentional **bad practices** in database schema and queries to demonstrate what Laravel Slower can detect and help fix.

### CRUDs Implemented

1. **Posts** - Blog posts with categories, tags, and comments
2. **Categories** - Post categories
3. **Tags** - Post tags (many-to-many relationship)
4. **Comments** - Comments on posts with nested replies

### Intentional Bad Practices

#### Database Schema Issues (Missing Indexes)

**Categories Table** (`database/migrations/2025_10_21_000001_create_categories_table.php`)
- ❌ No index on `slug` column (frequently used in WHERE clauses)

**Posts Table** (`database/migrations/2025_10_21_000002_create_posts_table.php`)
- ❌ No index on `slug` column
- ❌ No index on `user_id` (foreign key)
- ❌ No index on `category_id` (foreign key)
- ❌ No index on `is_published` (frequently filtered)
- ❌ No index on `published_at` (used for sorting)
- ❌ No composite index on `(is_published, published_at)` for common queries

**Tags Table** (`database/migrations/2025_10_21_000003_create_tags_table.php`)
- ❌ No index on `name` or `slug`

**Post_Tag Pivot Table** (`database/migrations/2025_10_21_000004_create_post_tag_table.php`)
- ❌ No composite index on `(post_id, tag_id)`
- ❌ No individual indexes on foreign keys

**Comments Table** (`database/migrations/2025_10_21_000005_create_comments_table.php`)
- ❌ No index on `post_id` (foreign key)
- ❌ No index on `user_id` (foreign key)
- ❌ No index on `parent_id` (self-referencing foreign key)
- ❌ No index on `is_approved` (frequently filtered)
- ❌ No index on `created_at` (used for sorting)
- ❌ No composite index on `(post_id, is_approved, created_at)`

#### Query Performance Issues (N+1 Queries)

**PostController** (`app/Http/Controllers/PostController.php`)
- ❌ `index()`: No eager loading of user, category, tags, comments
- ❌ `show()`: No eager loading causing N+1 for user, category, tags, comments, comment users, comment replies
- ❌ No pagination

**CategoryController** (`app/Http/Controllers/CategoryController.php`)
- ❌ `index()`: Uses `posts()->count()` in loop causing N+1
- ❌ `show()`: No eager loading of posts' relationships
- ❌ No pagination

**TagController** (`app/Http/Controllers/TagController.php`)
- ❌ `index()`: Uses `posts()->count()` in loop
- ❌ `show()`: No eager loading of posts' users and categories

**CommentController** (`app/Http/Controllers/CommentController.php`)
- ❌ `index()`: No eager loading of post and user
- ❌ Filtering on unindexed `is_approved` column
- ❌ No pagination

## Setup Instructions

1. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database** in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_slower_demo
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run migrations and seed data:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   This will create:
   - 10 users
   - 8 categories
   - 10 tags
   - 100 posts with random relationships
   - Comments with nested replies

5. **Install Laravel Slower:**
   ```bash
   composer require halilcosdu/laravel-slower --dev
   ```

6. **Publish Laravel Slower config (optional):**
   ```bash
   php artisan vendor:publish --tag=slower-config
   ```

7. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Testing Laravel Slower

Visit these pages to trigger the bad queries and see Laravel Slower's suggestions:

1. **Posts Index**: `http://localhost:8000/posts`
   - Will show N+1 queries for users, categories, tags, and comment counts

2. **Post Show**: `http://localhost:8000/posts/1`
   - Will show N+1 queries for tags, comments, comment users, and replies

3. **Categories Index**: `http://localhost:8000/categories`
   - Will show N+1 queries from `posts()->count()`

4. **Category Show**: `http://localhost:8000/categories/1`
   - Will show N+1 queries for posts' users

5. **Tags Index**: `http://localhost:8000/tags`
   - Will show N+1 queries from `posts()->count()`

6. **Tag Show**: `http://localhost:8000/tags/1`
   - Will show N+1 queries for posts' users and categories

7. **Comments Index**: `http://localhost:8000/comments`
   - Will show N+1 queries for posts and users

Laravel Slower should detect and suggest:
- Adding indexes to frequently queried columns
- Adding indexes to foreign keys
- Using eager loading to prevent N+1 queries
- Adding composite indexes for common query patterns
- Using pagination for large datasets

## Expected Improvements

After following Laravel Slower's suggestions, you should:

1. Add indexes to all foreign keys
2. Add indexes to frequently filtered columns (`slug`, `is_published`, etc.)
3. Add composite indexes for common query patterns
4. Implement eager loading in controllers:
   ```php
   // Instead of: Post::where('is_published', true)->get()
   Post::with(['user', 'category', 'tags', 'comments.user'])
       ->where('is_published', true)
       ->paginate(20);
   ```

## Routes

- `GET /posts` - List all posts
- `GET /posts/create` - Create post form
- `POST /posts` - Store new post
- `GET /posts/{post}` - Show post
- `GET /posts/{post}/edit` - Edit post form
- `PUT /posts/{post}` - Update post
- `DELETE /posts/{post}` - Delete post

Similar routes exist for `/categories`, `/tags`, and `/comments`.

## Authentication

Some features require authentication:
- Creating/editing posts, categories, tags
- Adding comments
- Approving comments

Default test user:
- Email: `test@example.com`
- Password: `password`

## File Structure

```
app/
├── Http/Controllers/
│   ├── CategoryController.php
│   ├── CommentController.php
│   ├── PostController.php
│   └── TagController.php
├── Models/
│   ├── Category.php
│   ├── Comment.php
│   ├── Post.php
│   └── Tag.php
database/
├── migrations/
│   ├── 2025_10_21_000001_create_categories_table.php
│   ├── 2025_10_21_000002_create_posts_table.php
│   ├── 2025_10_21_000003_create_tags_table.php
│   ├── 2025_10_21_000004_create_post_tag_table.php
│   └── 2025_10_21_000005_create_comments_table.php
└── seeders/
    ├── BlogSeeder.php
    └── DatabaseSeeder.php
resources/views/
├── layouts/
│   └── main.blade.php
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── posts/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── tags/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── comments/
    └── index.blade.php
```

---

**Note:** All bad practices are intentional for testing Laravel Slower. Do NOT use this code in production!
