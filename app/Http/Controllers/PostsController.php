<?php

namespace App\Http\Controllers;

use App\Enums\PlatformType;
use App\Models\Account;
use App\Models\Post;
use App\Services\TiptapContentConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostsController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts', [
            'accounts' => Account::query()
                ->where('user_id', Auth::id())
                ->get()
                ->map(function ($account) {
                    return [
                        'key' => $account->id,
                        'id' => $account->id,
                        'name' => $account->name,
                        'handle' => $account->handle,
                        'profile_image_url' => $account->profile_image_url,
                        'sociable_id' => $account->sociable_id,
                        'sociable_type' => $account->sociable_type,
                        'platform' => $account->platform,
                    ];
                }),
        ]);
    }

    public function getPosts(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'statuses' => 'nullable|array',
            'statuses.*' => 'string',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string',
            'accounts' => 'nullable|array',
            'accounts.*' => 'integer',
        ]);

        try {
            $query = Post::query()->with('account')
                ->where('user_id', Auth::id())
                ->whereBetween('created_at', [$request->start_date, $request->end_date]);

            // Apply the status filter if provided
            if ($request->filled('statuses')) {
                $query->whereIn('status', $request->statuses);
            }

            // Apply the platform filter if provided
            if ($request->filled('platforms')) {
                $query->whereIn('platform', $request->platforms);
            }

            // Apply the account filter if provided
            if ($request->filled('accounts')) {
                $query->whereIn('account_id', $request->accounts);
            }

            $posts = $query->get();

            $posts = $posts->map(function ($post) {
                return [
                    'account_id' => $post->account_id,
                    'platform' => $post->account->platform,
                    'platform_label' => PlatformType::from($post->account->platform)->getPlatformLabel(),
                    'content' => TiptapContentConverter::convertToText($post->content),
                    'status' => $post->status,
                    'post_id' => $post->post_id,
                    'published_at' => $post->published_at ? now()->parse($post->published_at) : null,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Posts retrieved successfully',
                'data' => $posts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ]);
        }
    }
}
