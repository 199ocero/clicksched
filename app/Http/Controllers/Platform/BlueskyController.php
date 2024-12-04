<?php

namespace App\Http\Controllers\Platform;

use App\Enums\PlatformType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bluesky\Store;
use App\Models\Account;
use App\Models\Platforms\Bluesky;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlueskyController extends Controller
{
    protected $publicBaseUrl = 'https://public.api.bsky.app/xrpc/';

    protected $authBaseUrl = 'https://bsky.social/xrpc/';

    /**
     * Store a newly created Bluesky account.
     */
    public function store(Store $request): RedirectResponse
    {
        try {

            $authResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->authBaseUrl.'com.atproto.server.createSession', [
                'identifier' => $request->handle,
                'password' => $request->app_password,
            ]);

            if ($authResponse->status() !== 200) {
                $errorData = $authResponse->json();
                throw new Exception(
                    'Incorrect handle or password',
                    $authResponse->status()
                );
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get($this->publicBaseUrl.'app.bsky.actor.getProfile', [
                'actor' => $request->handle,
            ]);

            switch ($response->status()) {
                case 200:
                    break;
                case 400:
                    $errorData = $response->json();
                    throw new Exception(
                        $errorData['message'] ?? 'Invalid Request',
                        400
                    );

                case 401:
                    $errorData = $response->json();
                    throw new Exception(
                        $errorData['message'] ?? 'Unauthorized Access',
                        401
                    );

                default:
                    throw new Exception(
                        $response->json()['message'] ?? 'Unknown error occurred',
                        500
                    );
            }

            return DB::transaction(function () use ($request, $response) {
                Account::query()->create([
                    'user_id' => auth()->user()->id,
                    'sociable_id' => null,
                    'sociable_type' => null,
                    'platform' => PlatformType::BLUESKY->value,
                    'name' => $response->json()['displayName'] ?? null,
                    'handle' => $request->handle ?? null,
                    'email' => null,
                    'profile_image_url' => $response->json()['avatar'] ?? null,
                    'access_token' => Crypt::encrypt($request->app_password),
                    'refresh_token' => null,
                    'token_expires_at' => null,
                ]);

                return redirect()->route('accounts.index')
                    ->with('flash_success', [
                        'title' => 'Account Connected',
                        'message' => 'Your account has been connected successfully.',
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

    /**
     * Delete a Bluesky account connection.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $account = Account::findOrFail($request->account_id);

                if ($account->user_id !== auth()->id() || $account->platform !== PlatformType::BLUESKY->value) {
                    throw new Exception('Unauthorized account deletion');
                }

                if ($account->sociable_id && $account->sociable_type) {
                    $sociableModel = $account->sociable_type::find($account->sociable_id);
                    if ($sociableModel) {
                        $sociableModel->delete();
                    }
                }

                $account->delete();

                return redirect()->route('accounts.index')
                    ->with('flash_success', [
                        'title' => 'Account Disconnected',
                        'message' => 'Your Bluesky account has been disconnected successfully.',
                        'duration' => 2500,
                    ]);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->route('accounts.index')
                ->with('flash_error', [
                    'title' => 'Disconnection Error',
                    'message' => $e->getMessage(),
                    'duration' => 2500,
                ]);
        }
    }
}
