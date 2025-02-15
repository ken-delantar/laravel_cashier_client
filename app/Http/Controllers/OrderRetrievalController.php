<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;

class OrderRetrievalController extends Controller
{
    public function __construct(){
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function user_order_retrieval(){
        $user = Auth::user();

        $customer = \Stripe\Customer::all(['email' => $user->email]);
    
        if (empty($customer->data)) {
            echo 'No Data.';
            return;
        }
    
        $customer = $customer->data[0];
        $orders = \Stripe\Checkout\Session::all(['customer' => $customer->id]);
    
        if (empty($orders->data)) {
            echo 'No orders found.';
            return;
        }
    
        $orders = array_filter($orders->data, function($item) use ($customer){
            return $item->mode == 'payment' && $item->customer == $customer->id && $item->status == 'complete';
        });
    
        if (empty($orders)) {
            echo 'No completed orders found.';
            return;
        }
    
        foreach($orders as $order){
            echo 'Order ID: ' . $order->id . '<br>';
            echo 'Status: ' . ucfirst($order->metadata->order_status) . '<br>';
            $created_at = date('F j, Y, g:i a', $order->created);
            echo 'Created At: ' . $created_at . '<br>';
            $amount_total = $order->amount_total / 100;
            echo 'Total Amount: ' . number_format($amount_total, 2) . '<br>';
            echo 'Product(s): <br>';
            
            $order_items = $order->allLineItems($order->id);
            foreach($order_items as $item){
                echo $item->description . ' ..... ' . number_format($item->amount_total / 100, 2) . '<br>';
            }
            echo '<hr>';
        }
    }
    
}
