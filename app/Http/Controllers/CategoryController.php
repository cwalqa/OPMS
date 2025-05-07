<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::paginate(10);
        return view('admin.inventory.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create($request->all());
        return redirect()->route('inventory.categories')->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());
        return redirect()->route('inventory.categories')->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('inventory.categories')->with('success', 'Category deleted successfully.');
    }
}
