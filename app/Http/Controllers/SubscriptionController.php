<?php

namespace App\Http\Controllers;

use App\Http\Middleware\isEmployer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    const WEEKLY_AMOUNT = 6.99;
    const MONTHLY_AMOUNT = 24.99;
    const YEARLY_AMOUNT = 270.00;
    const CURRENCY = 'USD';

    public function subscribe()
    {
        return view('subscription.index');
    }

    public function initiatePayment(Request $request)
    {
        $plans = [
            'weekly' => [
                'name' => 'weekly',
                'description' => 'Weekly payment',
                'amount' => self::WEEKLY_AMOUNT*100,
                'currency' => self::CURRENCY,
                'quantity' => 1,
            ],
            'monthly' => [
                'name' => 'monthly',
                'description' => 'Monthly payment',
                'amount' => self::MONTHLY_AMOUNT*100,
                'currency' => self::CURRENCY,
                'quantity' => 1,
            ],
            'yearly' => [
                'name' => 'yearly',
                'description' => 'Yearly payment',
                'amount' => self::YEARLY_AMOUNT*100,
                'currency' => self::CURRENCY,
                'quantity' => 1,
            ],
        ];

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $selectPlan = null;
            if ($request->is('pay/weekly')) {
                $selectPlan = $plans['weekly'];
                $billingEnds = now()->addWeek()->startOfDay()->toDateString();
            }
            elseif ($request->is('pay/monthly')) {
                $selectPlan = $plans['monthly'];
                $billingEnds = now()->addMonth()->startOfDay()->toDateString();
            }
            elseif ($request->is('pay/yearly')) {
                $selectPlan = $plans['yearly'];
                $billingEnds = now()->addYear()->startOfDay()->toDateString();
            }
            if($selectPlan) {
                $successURL = URL::signedRoute('payment.success', [
                    'plan' => $selectPlan['name'],
                    'billing_ends' => $billingEnds,
                ]);

                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price_data' => [
                            'currency' => $selectPlan['currency'],
                            'unit_amount' => $selectPlan['amount'], // Now using `unit_amount`
                            'product_data' => [
                            'name' => ucfirst($selectPlan['name']), // Capitalized name
                            'description' => $selectPlan['description'],
                        ],
                    ],
                    'quantity' => 1,
                        ]
                    ],
                    'mode' => 'payment',
                    'success_url' => $successURL,
                    'cancel_url' => route('payment.cancel'),
                ]);
                return redirect($session->url);
            }
        }
        catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function paymentSuccess(Request $request)
    {

        $plan = $request->plan;
        $billingEnds = $request->billing_ends;
        User::where('id', auth()->user()->id)->update([
            'plan' =>$plan,
            'billing_ends' => $billingEnds,
            'status' => 'paid'
        ]);

        return redirect()->route('dashboard')->with('success', 'Payment was successfully processed');


        // $plan = $request->plan;
        // $billingEnds = $request->billing_ends;
    
        // $user = User::find($request->user_id); // Use a user identifier passed in the signed route
    
        // if (!$user) {
        //     return redirect()->route('dashboard')->with('error', 'User not found.');
        // }
    
        // $user->update([
        //     'plan' => $plan,
        //     'billing_ends' => $billingEnds,
        //     'status' => 'paid',
        // ]);
    
        // return redirect()->route('dashboard')->with('success', 'Payment was successfully processed');
    }
    

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error', 'Payment was unsuccessful!');
    }
}
