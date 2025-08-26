<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SubjectPageController extends Controller
{
    public function index(): View
    {
        return view('subjects.index');
    }

    public function create(): View
    {
        return view('subjects.create');
    }

    public function edit(int $id): View
    {
        return view('subjects.edit', ['id' => $id]);
    }
}
