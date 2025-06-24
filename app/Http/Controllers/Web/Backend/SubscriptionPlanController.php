<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller {
    public function index() {
        $plans = SubscriptionPlan::get();
        return view('backend.layouts.subscription-plan.index', compact('plans'));
    }

    public function toggleStatus(SubscriptionPlan $subscription_plan) {
        // Flip status
        $newStatus                 = $subscription_plan->status === 'active' ? 'inactive' : 'active';
        $subscription_plan->status = $newStatus;

        // If we're deactivating this plan, strip out any recommendation flag
        if ($newStatus === 'inactive' && $subscription_plan->is_recommended) {
            $subscription_plan->is_recommended = false;
        }

        $subscription_plan->save();

        return redirect()
            ->route('subscription-plan.index')
            ->with('t-success', 'Plan status and recommendation updated.');
    }

    public function toggleRecommended(SubscriptionPlan $subscription_plan) {
        // If this plan is being set as recommended, first remove recommended status from all plans
        if (!$subscription_plan->is_recommended) {
            SubscriptionPlan::where('is_recommended', true)->update(['is_recommended' => false]);
        }

        // Toggle the recommended status for this plan
        $subscription_plan->is_recommended = !$subscription_plan->is_recommended;
        $subscription_plan->save();

        return redirect()->route('subscription-plan.index')
            ->with('t-success', $subscription_plan->is_recommended ? 'Plan set as recommended.' : 'Plan removed from recommended.');
    }

    public function update(Request $request, $id) {
        $plan = SubscriptionPlan::findOrFail($id);

        // Validate inputs
        $validated = $request->validate([
            'name'             => 'required|string',
            'billing_interval' => 'required|string|in:month,year',
            'price'            => 'required|numeric',
            'currency'         => 'required|string',
            'description'      => 'nullable|string',
            'features'         => 'array', // This expects an array in the request
            'status'           => 'required|in:active,inactive',
            'is_recommended'   => 'boolean',
        ]);

        // Parse comma-separated features into an array if needed
        // (e.g. user typed "aaa, bbb, ccc" in a single field)
        if (!empty($request->input('features'))) {
            // If your form sends features as an array with one item:
            // "features[0] = 'aaa, bbb, ccc'"
            $featuresString        = $request->input('features')[0] ?? '';
            $featuresArray         = array_map('trim', explode(',', $featuresString));
            $validated['features'] = $featuresArray;
        }

        // Update model
        $plan->update($validated);

        return redirect()
            ->route('subscription-plan.index')
            ->with('t-success', 'Plan updated successfully.');
    }
}
