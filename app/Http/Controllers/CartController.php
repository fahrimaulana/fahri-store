<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $carts = Cart::with(['product.galleries', 'user'])->where('user_id', Auth::user()->id)->get();
        // foreach ($carts as $cart) {
        //     dd($cart->product->galleries->first());
        // }
        return view('pages.cart', compact('carts'));
    }

    public function success()
    {
        return view('pages.success');
    }

    public function delete(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return redirect()->route('cart');
    }
}
