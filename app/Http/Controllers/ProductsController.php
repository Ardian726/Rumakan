<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategories;
use App\Models\Products;

class ProductsController extends Controller
{
    public function index()
    {
        $categories = ProductCategories::all();
        $products = Products::with('category')->get();
        return view('products', compact('products', 'categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'product_name' => 'required|max:100',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'image1_url' => 'required|max:255',
        ]);

        Products::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $products = Products::findOrFail($id);
        $categories = ProductCategories::all();
        return view('products', compact('products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $products = Products::findOrFail($id);

        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'product_name' => 'required|max:100',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'image1_url' => 'required|max:255',
        ]);

        $products->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $products = Products::findOrFail($id);
        $products->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
