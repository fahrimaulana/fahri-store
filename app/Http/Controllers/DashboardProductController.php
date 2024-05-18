<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DashboardProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['galleries', 'category'])
            ->where('user_id', Auth::user()->id)
            ->get();

        return view('pages.dashboard-products', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('pages.dashboard-product-create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $products = Product::create($data);
        $galleries = [
            'product_id' => $products->id,
            'photo' => $request->file('photo')->store('assets/product', 'public')
        ];
        ProductGallery::create($galleries);

        return redirect()->route('dashboard-products');
    }

    public function details(Request $request, $id)
    {
        $product = Product::with(['galleries', 'user', 'category'])->findOrFail($id);
        $categories = Category::all();

        return view('pages.dashboard-product-details', compact('product', 'categories'));
    }

    public function uploadGallery(Request $request)
    {
        $data = $request->all();
        $data['photo'] = $request->file('photo')->store('assets/product', 'public');

        ProductGallery::create($data);

        return redirect()->route('dashboard-product-details', $request->product_id);
    }

    public function deleteGallery(Request $request, $id)
    {
        $item = ProductGallery::FindOrFail($id);
        $path = storage_path('app/public/' . $item->photo);
        if(File::exists($path)) {
            File::delete($path);
        }
        $item->delete();


        return redirect()->route('dashboard-product-details', $item->product_id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $item = Product::FindOrFail($id);
        $data['slug'] = Str::slug($request->name);

        $item->update($data);

        return redirect()->route('dashboard-products');
    }
}
