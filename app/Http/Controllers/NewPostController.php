<?php

namespace App\Http\Controllers;

use App\Enums\PlatformType;
use App\Enums\PostStatusType;
use App\Http\Requests\NewPost\StoreRequest;
use App\Jobs\PublishPost;
use App\Models\Account;
use App\Models\NewPost;
use App\Models\Post;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class NewPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('NewPost', [
            'accounts' => Account::query()->get()
                ->groupBy('platform')
                ->map(function ($accounts, $platform) {
                    return [
                        PlatformType::from($platform)->value => $accounts->map(function ($account) {
                            return [
                                'id' => $account->id,
                                'name' => $account->name,
                                'handle' => $account->handle,
                                'profile_image_url' => $account->profile_image_url,
                                'sociable_id' => $account->sociable_id,
                                'sociable_type' => $account->sociable_type,
                                'platform_identifier' => $account->platform,
                                'platform_title' => PlatformType::from($account->platform)->getPlatformLabel(),
                            ];
                        }),
                    ];
                })
                ->collapse(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                foreach ($request->social_account_ids as $accountId) {
                    $account = Account::query()->findOrFail($accountId);

                    $post = Post::query()->create([
                        'user_id' => Auth::id(),
                        'account_id' => $account->id,
                        'platform' => $account->platform,
                        'content' => $request->content,
                        'status' => PostStatusType::QUEUED->value,
                        'published_at' => null,
                    ]);

                    dispatch(new PublishPost($account, $post, $request->content ?? [], $request->media ?? []));
                }

                return redirect()->route('new-post.index')
                    ->with('flash_success', [
                        'title' => 'Post Queued',
                        'message' => 'Your post has been queued and will be posted shortly.',
                        'duration' => 2500,
                    ]);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->route('accounts.index')
                ->with('flash_error', [
                    'title' => 'Bluesky Connection Error',
                    'message' => $e->getMessage(),
                    'duration' => 2500,
                ]);
        }
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(NewPost $newPost): NewPostResource
    // {
    //     return new NewPostResource($newPost);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, NewPost $newPost): NewPostResource
    // {
    //     $validatedData = $request->validate([
    //         // Add validation rules for updating new post
    //     ]);

    //     $newPost->update($validatedData);
    //     return new NewPostResource($newPost);
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(NewPost $newPost): Response
    // {
    //     $newPost->delete();
    //     return response()->noContent();
    // }
}
