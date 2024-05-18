<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $id)
    {
        $product = Product::with(['galleries', 'user'])->where('slug', $id)->firstOrFail();

        return view('pages.detail', compact('product'));
    }

    public function add(Request $request, $id)
    {
        $data = [
            'product_id' => $id,
            'user_id' => Auth::user()->id,
        ];

        Cart::create($data);

        return redirect()->route('cart');
    }
}
