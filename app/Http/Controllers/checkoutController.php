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
        
        $request->validate([
            'price_id' => 'required',
            'payment_method' => 'required',
        ]);

        try {
            $customers = \Stripe\Customer::all(['email' => $user->email]);
            if ($customers->isEmpty()) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name, 
                    'metadata' => [
                        'role' => 'buyer'
                    ]
                ]);                
            } else {
                $customer = $customers->data[0];
            }

            $price = \Stripe\Price::retrieve($request->price_id);
            
            $commissionRate = 0.05;
            $totalAmount = $price->unit_amount;
            $vendorAccount = $price->metadata->stripe_account;
            $commissionFee = round($totalAmount * $commissionRate);

            $checkoutSession = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'customer' => $customer->id,
                'payment_method_types' => [
                    $request->payment_method,
                ],
                'metadata' => [
                    'order_status' => 'Pending',
                    'vendor_id' => $vendorAccount
                ],
                'invoice_creation' => [
                    'enabled' => true
                ],
                'success_url' => $domain . '/product_listing?success=Checkout Success..',
                'cancel_url' => $domain . '/product_listing?cancel=Checkout Successfully Failed..',
                'payment_intent_data' => [
                    'application_fee_amount' => $commissionFee,
                    'transfer_data' => [
                        'destination' => $vendorAccount
                    ]
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
