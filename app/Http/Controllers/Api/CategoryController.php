<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function __construct()
    {
        CategoryResource::withoutWrapping();
    }

    public function index()
    {
        $data = Category::query()
            ->withCount('courses')
            ->get();

        return CategoryResource::collection($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'unique:categories,name'],
            'name_ar' => ['nullable', 'string'],
        ]);

        $category = Category::create($data);
        return CategoryResource::make($category);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'unique:categories,name,' . $category->id],
            'name_ar' => ['nullable', 'string'],
        ]);

        $category->update($data);
        return CategoryResource::make($category);
    }

    public function delete(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
