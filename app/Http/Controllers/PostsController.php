<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PostsController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts');
    }
}
