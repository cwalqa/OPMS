<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);
        return view('admin.inventory.brands', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands',
            'description' => 'nullable|string'
        ]);

        Brand::create($request->all());
        return redirect()->route('inventory.brands')->with('success', 'Brand added successfully.');
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string'
        ]);

        $brand->update($request->all());
        return redirect()->route('inventory.brands')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('inventory.brands')->with('success', 'Brand deleted successfully.');
    }
}
