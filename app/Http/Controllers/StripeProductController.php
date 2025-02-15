<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeProductController extends Controller
{
    //function to execute first, when this contoller is active
    public function __construct(){
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function product_retrieve(){
        $products = \Stripe\Product::all(
            ['type' => 'good']
        );
        $products = $products->data;
        $products = array_filter($products, function($item){
            return $item->metadata->status == 'Approved' && $item->type == 'good';
        });

        $prices = \Stripe\Price::all();

        return view('dashboard', compact('products', 'prices'));
    }
}
