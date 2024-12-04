<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewPostResource;
use App\Models\NewPost;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class NewPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('NewPost');
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request): NewPostResource
    // {
    //     $validatedData = $request->validate([
    //         // Add validation rules for new post
    //     ]);

    //     $newPost = NewPost::create($validatedData);
    //     return new NewPostResource($newPost);
    // }

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
