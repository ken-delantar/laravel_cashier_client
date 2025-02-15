<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkoutController extends Controller
{
    public function __construct(){
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function checkout(Request $request){
        $user = Auth::user();
        $domain = env('APP_URL');

        try {
            $customers = \Stripe\Customer::all(['email' => $user->email]);
            if ($customers->isEmpty()) {
                $customer = \Stripe\Customer::create(['email' => $user->email]);
            } else {
                $customer = $customers->data[0];
            }

            $price = \Stripe\Price::retrieve($request->price_id);
            $checkoutSession = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'customer' => $customer->id,
                'metadata' => [
                    'order_status' => 'Pending',
                    'vendor_id' => $price->metadata->stripe_account
                ],
                'invoice_creation' => [
                    'enabled' => true
                ],
                'success_url' => $domain . '/product_listing?success=Checkout Success..',
                'cancel_url' => $domain . '/product_listing?cancel=Checkout Successfully Failed..', 
                'payment_intent_data' => [
                    'transfer_data' => [
                        'destination' => $price->metadata->stripe_account
                    ],
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
