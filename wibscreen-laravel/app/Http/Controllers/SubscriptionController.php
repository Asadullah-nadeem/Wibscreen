<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class SubscriptionController extends Controller
{
    public function upgrade(Request $request)
    {
        $user = auth()->user();
        $plan = $request->query('plan');
        $duration = $request->query('duration', '1 Month'); // '1 Month' or '1 Year'
        $paymentId = $request->query('payment_id');

        if ($plan === 'pro') {
            // Calculate Expiry
            $expiry = $duration === '1 Year' ? Carbon::now()->addYear() : Carbon::now()->addMonth();
            
            $user->update([
                'plan' => 'pro',
                'plan_expiry_at' => $expiry,
                'plan_status' => 'active',
                'payment_id' => $paymentId
            ]);

            $message = "Pro Plan activated successfully for {$duration}! Your Expiry Date is: {$expiry->format('d M, Y')}. Thank you for choosing Wibscreen Pro.";
            return redirect()->route('dashboard')->with('success', $message);
        }

        if ($plan === 'business') {
            $user->update([
                'plan' => 'business',
                'plan_status' => 'pending'
            ]);

            $message = "Thanx Over Team Cannect soon then help you. Your request for the Business Plan has been received and is pending superadmin approval.";
            return redirect()->route('dashboard')->with('info', $message);
        }

        if ($plan === 'free') {
            $user->update([
                'plan' => 'free',
                'plan_expiry_at' => null,
                'plan_status' => 'active'
            ]);

            return redirect()->route('dashboard')->with('success', "You have switched to the Free plan.");
        }

        return redirect()->back()->with('error', 'Invalid plan selected.');
    }
}
