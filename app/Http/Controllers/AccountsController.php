<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountsController extends Controller
{
    public function index()
    {
        return Inertia::render('Accounts', [
            'accounts' => Account::query()
                ->where('user_id', Auth::id())
                ->get()
                ->map(function ($account) {
                    return [
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
}
