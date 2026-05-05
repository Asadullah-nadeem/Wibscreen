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
        $planSlug = $request->query('plan');
        $duration = $request->query('duration', '1 Month'); // '1 Month' or '1 Year'
        $paymentId = $request->query('payment_id');

        $plan = \App\Models\Plan::where('slug', $planSlug)->first();

        if (!$plan) {
            return redirect()->back()->with('error', 'Invalid plan selected.');
        }

        // --- NEW RESTRICTION LOGIC ---
        // Prevent active premium users from buying any plan (even a different one)
        if ($user->plan !== 'free' && !$user->isPlanExpired()) {
            return redirect()->back()->with('error', 'You already have an active ' . ucfirst($user->plan) . ' subscription. You can purchase a new plan once your current one expires.');
        }
        // -----------------------------

        // Handle Paid Plans (Pro & Business)
        if (in_array($planSlug, ['pro', 'business'])) {
            if (!$paymentId) {
                return redirect()->back()->with('error', 'Payment ID is required for ' . ucfirst($planSlug) . ' plan.');
            }

            $expiry = $duration === '1 Year' ? Carbon::now()->addYear() : Carbon::now()->addMonth();
            
            $user->update([
                'plan' => $planSlug,
                'plan_expiry_at' => $expiry,
                'plan_status' => 'active',
                'payment_id' => $paymentId
            ]);

            // Update Payment Lead Status
            \App\Models\PaymentLead::where('user_id', $user->id)
                ->where('plan_slug', $planSlug)
                ->where('status', '!=', 'completed')
                ->update(['status' => 'completed']);

            // Log Subscription History
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $plan->name . ' Plan',
                'plan_slug' => $planSlug,
                'amount' => ($duration === '1 Year' ? $plan->price_yearly : $plan->price_monthly),
                'payment_id' => $paymentId,
                'payment_status' => 'success',
                'starts_at' => Carbon::now(),
                'expires_at' => $expiry,
                'metadata' => ['duration' => $duration, 'tabs_at_signup' => $user->totalTabsCount()]
            ]);

            // Send Upgrade Confirmation Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PlanUpgradedEmail($user, $planSlug, $expiry));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send upgrade email to {$user->email}: " . $e->getMessage());
            }

            $message = ucfirst($planSlug) . " Plan activated successfully for {$duration}! Your Expiry Date is: {$expiry->format('d M, Y')}. Thank you for choosing Wibscreen.";
            return redirect()->route('dashboard')->with('success', $message);
        }

        if ($planSlug === 'free') {
            // Block manual downgrade if premium plan is still active
            if ($user->plan !== 'free' && !$user->isPlanExpired() && $user->plan_status === 'active') {
                return redirect()->back()->with('error', 'You cannot downgrade to the Free plan while your ' . ucfirst($user->plan) . ' plan is still active.');
            }

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
