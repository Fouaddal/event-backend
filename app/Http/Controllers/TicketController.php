<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class TicketController extends Controller
{
    public function buyTickets(Request $request, Event $event)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->quantity;

        // ✅ Check remaining capacity
        if ($event->capacity < $quantity) {
            return response()->json(['message' => 'Not enough tickets available'], 400);
        }

        $totalAmount = $quantity * $event->ticket_price;

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $totalAmount * 100, // Stripe works in cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'quantity' => $quantity
            ],
        ]);

        // ✅ Save ticket
        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'quantity' => $quantity,
            'amount' => $totalAmount,
            'stripe_payment_intent' => $paymentIntent->id,
        ]);

        // ✅ Reduce event capacity
        $event->decrement('capacity', $quantity);

        return response()->json([
            'message' => 'Ticket purchase initiated',
            'client_secret' => $paymentIntent->client_secret,
            'ticket' => $ticket
        ]);
    }
}
