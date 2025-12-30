<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use ServiceAction;

    protected function getModelClass(): string { return Category::class; }
    protected function getRequestClass(): string { return CategoryRequest::class; }


    public function index(Request $request)
    {
        return Inertia::render('master/Category', [
            'categories' => Category::where('user_id', auth()->user()->id)->get(),
        ]);
    }
}
