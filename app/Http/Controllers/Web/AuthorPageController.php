<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AuthorPageController extends Controller
{
    public function index(): View
    {
        return view('authors.index');
    }

    public function create(): View
    {
        return view('authors.create');
    }

    public function edit(int $id): View
    {
        return view('authors.edit', ['id' => $id]);
    }
}
