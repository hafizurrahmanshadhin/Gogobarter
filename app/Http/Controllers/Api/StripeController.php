<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller {
    public function __construct() {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkout(Request $request, SubscriptionPlan $plan): JsonResponse {
        $user = $request->user();

        // Free plan: assign directly
        if ($plan->price == 0) {
            UserSubscription::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'subscription_plan_id' => $plan->id,
                'status'               => 'active',
                'starts_at'            => now(),
                'ends_at'              => null,
                'amount_paid'          => 0,
                'payment_method'       => null,
                'transaction_id'       => null,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Free plan activated.',
            ]);
        }

        // Get URLs from request or use default
        $successUrl = $request->input('success_url', config('app.frontend_url') . '/subscription/success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl  = $request->input('cancel_url', config('app.frontend_url') . '/subscription/cancel');

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'payment',
            'line_items'           => [[
                'price_data' => [
                    'currency'     => strtolower($plan->currency),
                    'unit_amount'  => intval($plan->price * 100),
                    'product_data' => [
                        'name'        => $plan->name . ' Plan',
                        'description' => $plan->description,
                    ],
                ],
                'quantity'   => 1,
            ]],
            'customer_email'       => $user->email,
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'metadata'             => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'payment_intent_data'  => [
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Checkout session created.',
            'data'    => ['id' => $session->id, 'url' => $session->url],
        ], 201);
    }

    public function webhook(Request $request) {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (Exception $e) {
            return response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session  = $event->data->object;
            $metadata = $session->metadata ?? [];

            $userId = $metadata->user_id ?? null;
            $planId = $metadata->plan_id ?? null;

            if ($userId && $planId) {
                $plan   = SubscriptionPlan::find($planId);
                $endsAt = null;

                if ($plan) {
                    $endsAt = $plan->billing_interval === 'year'
                    ? now()->addYear()
                    : now()->addMonth();
                }

                UserSubscription::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'subscription_plan_id' => $planId,
                        'status'               => 'active',
                        'starts_at'            => now(),
                        'ends_at'              => $endsAt,
                        'amount_paid'          => $session->amount_total / 100,
                        'payment_method'       => 'stripe',
                        'transaction_id'       => $session->payment_intent,
                    ]
                );
            }
        }

        return response('Webhook handled', 200);
    }

}
