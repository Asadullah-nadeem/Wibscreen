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
            $expiry = $duration === '1 Year' ? Carbon::now()->addYear() : Carbon::now()->addMonth();
            
            $user->update([
                'plan' => 'pro',
                'plan_expiry_at' => $expiry,
                'plan_status' => 'active',
                'payment_id' => $paymentId
            ]);

            // Log Subscription History
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_name' => 'Pro Plan',
                'plan_slug' => 'pro',
                'amount' => ($duration === '1 Year' ? 1999 : 199),
                'payment_id' => $paymentId,
                'payment_status' => 'success',
                'starts_at' => Carbon::now(),
                'expires_at' => $expiry,
                'metadata' => ['duration' => $duration, 'tabs_at_signup' => $user->totalTabsCount()]
            ]);

            // Send Upgrade Confirmation Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PlanUpgradedEmail($user, 'pro', $expiry));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send upgrade email to {$user->email}: " . $e->getMessage());
            }

            $message = "Pro Plan activated successfully for {$duration}! Your Expiry Date is: {$expiry->format('d M, Y')}. Thank you for choosing Wibscreen Pro.";
            return redirect()->route('dashboard')->with('success', $message);
        }

        if ($plan === 'business') {
            $user->update([
                'plan' => 'business',
                'plan_status' => 'pending'
            ]);

            // Log Pending Request
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_name' => 'Business Plan',
                'plan_slug' => 'business',
                'amount' => 0,
                'payment_status' => 'pending',
                'starts_at' => \Carbon\Carbon::now(),
                'metadata' => ['tabs_at_signup' => $user->totalTabsCount()]
            ]);

            // Send Dual Notifications
            try {
                // 1. Notify the Team (Sales)
                \Illuminate\Support\Facades\Mail::to('support.codeaxe@gmail.com')->send(new \App\Mail\BusinessRequestTeamEmail($user));
                
                // 2. Confirm to the User
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\BusinessRequestUserEmail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send Business request emails: " . $e->getMessage());
            }

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
