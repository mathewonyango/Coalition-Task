<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('Products.index');
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product);
    }

    public function fetch()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        // dd($products);
        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $product = Product::find($id);
        $product->update($request->all());
        return response()->json($product);
    }
}
