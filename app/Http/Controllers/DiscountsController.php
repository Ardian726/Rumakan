<?php

namespace App\Http\Controllers;

use App\Models\Discounts;
use App\Models\DiscountCategories;
use App\Models\Products;
use Illuminate\Http\Request;

class DiscountsController extends Controller
{
    public function index()
    {
        $discounts = Discounts::with(['category', 'product'])->get();
        return view('discounts', compact('discounts'));
    }

    public function create()
    {
        $categories = DiscountCategories::all();
        $products = Products::all();
        return view('discounts.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_discount_id' => 'required|exists:discount_categories,id',
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        Discounts::create($request->all());

        return redirect()->route('discount.index')->with('success', 'Discount created successfully.');
    }

    public function edit($id)
    {
        $discounts = Discounts::findOrFail($id);
        $categories = DiscountCategories::all();
        $products = Products::all();
        return view('discounts.edit', compact('discounts', 'categories', 'products'));
    }

    public function update(Request $request, $id)
    {
        $discounts = Discounts::findOrFail($id);

        $request->validate([
            'category_discount_id' => 'required|exists:discount_categories,id',
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        $discounts->update($request->all());

        return redirect()->route('discount.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy($id)
    {
        $discounts = Discounts::findOrFail($id);
        $discounts->delete();

        return redirect()->route('discount.index')->with('success', 'Discount deleted successfully.');
    }
}
