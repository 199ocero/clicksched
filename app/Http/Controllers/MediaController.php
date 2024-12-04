<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        return Inertia::render('Media');
    }
}
