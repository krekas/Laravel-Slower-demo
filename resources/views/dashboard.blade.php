<x-layouts.app :title="__('Dashboard - Laravel Slower Demo')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Overall Stats --}}
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Posts</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($overallStats->total_posts) }}</div>
                <div class="mt-1 text-xs text-neutral-500">{{ number_format($overallStats->published_posts) }} published</div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Comments</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($overallStats->total_comments) }}</div>
                <div class="mt-1 text-xs text-neutral-500">{{ number_format($overallStats->approved_comments) }} approved</div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Views</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($overallStats->total_views) }}</div>
                <div class="mt-1 text-xs text-neutral-500">{{ number_format($overallStats->avg_views_per_post, 1) }} avg/post</div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Active Users</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($overallStats->total_users) }}</div>
                <div class="mt-1 text-xs text-neutral-500">{{ number_format($overallStats->active_authors_week) }} authors this week</div>
            </div>
        </div>

        {{-- Top Engaging Posts --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-xl font-semibold">Top Engaging Posts</h2>
                <p class="mt-1 text-sm text-neutral-500">Posts ranked by engagement score (views, comments, tags)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-neutral-200 dark:border-neutral-700">
                        <tr class="text-left text-sm text-neutral-500">
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Views</th>
                            <th class="px-6 py-3">Comments</th>
                            <th class="px-6 py-3">Tags</th>
                            <th class="px-6 py-3">Engagement Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($topEngagingPosts as $post)
                        <tr class="text-sm">
                            <td class="px-6 py-4 font-medium">{{ Str::limit($post->title, 50) }}</td>
                            <td class="px-6 py-4">{{ number_format($post->view_count) }}</td>
                            <td class="px-6 py-4">{{ number_format($post->comment_count) }}</td>
                            <td class="px-6 py-4">{{ number_format($post->tag_count) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ number_format($post->engagement_score, 1) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Top Active Users --}}
            <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                    <h2 class="text-xl font-semibold">Top Active Users</h2>
                    <p class="mt-1 text-sm text-neutral-500">Users with most posts and comments</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($topActiveUsers as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="mt-1 text-xs text-neutral-500">
                                    {{ number_format($user->post_count) }} posts •
                                    {{ number_format($user->comment_count) }} comments •
                                    {{ number_format($user->total_views) }} views
                                </div>
                            </div>
                            <div class="ml-4 text-sm font-semibold text-neutral-500">
                                {{ number_format($user->received_comments) }} replies received
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Popular Tags --}}
            <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                    <h2 class="text-xl font-semibold">Popular Tags</h2>
                    <p class="mt-1 text-sm text-neutral-500">Tags ranked by popularity and usage</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-3">
                        @foreach($popularTags as $tag)
                        <div class="inline-flex items-center gap-2 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700">
                            <span class="font-medium">{{ $tag->name }}</span>
                            <span class="rounded-full bg-neutral-200 px-2 py-0.5 text-xs font-semibold dark:bg-neutral-600">
                                {{ number_format($tag->usage_count) }}
                            </span>
                            <span class="text-xs text-neutral-500">
                                {{ number_format($tag->total_tag_views) }} views
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Stats --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-xl font-semibold">Category Performance</h2>
                <p class="mt-1 text-sm text-neutral-500">Detailed statistics per category</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-neutral-200 dark:border-neutral-700">
                        <tr class="text-left text-sm text-neutral-500">
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Posts</th>
                            <th class="px-6 py-3">Avg Views</th>
                            <th class="px-6 py-3">Total Comments</th>
                            <th class="px-6 py-3">Unique Tags</th>
                            <th class="px-6 py-3">Recent Posts (30d)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($categoryStats as $category)
                        <tr class="text-sm">
                            <td class="px-6 py-4 font-medium">{{ $category->name }}</td>
                            <td class="px-6 py-4">{{ number_format($category->post_count) }}</td>
                            <td class="px-6 py-4">{{ number_format($category->avg_views, 1) }}</td>
                            <td class="px-6 py-4">{{ number_format($category->total_comments) }}</td>
                            <td class="px-6 py-4">{{ number_format($category->unique_tags) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ number_format($category->recent_posts) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monthly Trends --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-xl font-semibold">Monthly Trends</h2>
                <p class="mt-1 text-sm text-neutral-500">Content creation and engagement over time</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-neutral-200 dark:border-neutral-700">
                        <tr class="text-left text-sm text-neutral-500">
                            <th class="px-6 py-3">Month</th>
                            <th class="px-6 py-3">Posts Created</th>
                            <th class="px-6 py-3">Comments Created</th>
                            <th class="px-6 py-3">Total Views</th>
                            <th class="px-6 py-3">Active Authors</th>
                            <th class="px-6 py-3">Avg Comments/Post</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($monthlyStats as $stat)
                        <tr class="text-sm">
                            <td class="px-6 py-4 font-medium">{{ $stat->month }}</td>
                            <td class="px-6 py-4">{{ number_format($stat->posts_created) }}</td>
                            <td class="px-6 py-4">{{ number_format($stat->comments_created) }}</td>
                            <td class="px-6 py-4">{{ number_format($stat->total_views) }}</td>
                            <td class="px-6 py-4">{{ number_format($stat->active_authors) }}</td>
                            <td class="px-6 py-4">{{ number_format($stat->avg_comments_per_post, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Deepest Comment Threads --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-xl font-semibold">Most Discussed Comments</h2>
                <p class="mt-1 text-sm text-neutral-500">Parent comments with the most replies</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($deepestThreads as $thread)
                    <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                        <div class="mb-2 text-sm font-medium text-neutral-500">
                            On post: {{ Str::limit($thread->post_title, 60) }}
                        </div>
                        <div class="text-sm">{{ Str::limit($thread->content, 150) }}</div>
                        <div class="mt-3 flex gap-4 text-xs text-neutral-500">
                            <span>{{ number_format($thread->direct_replies) }} direct replies</span>
                            <span>{{ number_format($thread->nested_replies) }} nested replies</span>
                            <span>{{ number_format($thread->content_length) }} characters</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
