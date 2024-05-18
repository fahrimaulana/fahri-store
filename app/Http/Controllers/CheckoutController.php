<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // save user data
        $user = Auth::user();
        $user->update($request->except('total_price'));

        // process checkout
        $code = 'STORE-' . mt_rand(000000,999999);
        $carts = Cart::with(['product', 'user'])
                    ->where('user_id', Auth::user()->id)
                    ->get();

        // transaction create
        $transactions = Transaction::create([
            'user_id' => Auth::user()->id,
            'insurance_price' => 0,
            'shipping_price' => 0,
            'total_price' => $request->total_price,
            'transaction_status' => 'PENDING',
            'code' => $code
        ]);

        foreach ($carts as $cart) {
            $trx = 'TRX-' . mt_rand(000000,999999);

            TransactionDetail::create([
                'transaction_id' => $transactions->id,
                'product_id' => $cart->product->id,
                'price' => $cart->product->price,
                'shipping_status' => 'PENDING',
                'resi' => '',
                'code' => $trx
            ]);
        }

        // Delete Cart Data
        Cart::where('user_id', Auth::user()->id)->delete();

        // Configuration Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Send to midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $code,
                'gross_amount' => (int) $request->total_price
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'enabled_payments' => [
                'gopay', 'bank_traansfer'
            ],
            'vtweb' => []
        ];

        try {
            $paymentUrl = Snap::createTransaction($params)->redirect_url;

            return redirect($paymentUrl);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function callback(Request $request)
    {

    }
}
