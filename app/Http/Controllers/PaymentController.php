<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function charge(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric',
        'token' => 'required|string',
    ]);

    Stripe::setApiKey(env('STRIPE_SECRET'));

    $charge = Charge::create([
        'amount' => $request->amount * 100,
        'currency' => 'usd',
        'source' => $request->token,
        'description' => 'Event Booking Payment',
    ]);

    return response()->json(['message' => 'Payment successful', 'charge' => $charge]);
}
}
