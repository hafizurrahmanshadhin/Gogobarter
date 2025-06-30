<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller {
    /**
     * Display the list of subscription plans.
     *
     * @return JsonResponse|View
     * @throws Exception
     */
    public function index(): JsonResponse | View {
        try {
            $plans = SubscriptionPlan::get();
            return view('backend.layouts.subscription-plan.index', compact('plans'));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle the status of a subscription plan.
     *
     * @param SubscriptionPlan $subscription_plan
     * @return JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function toggleStatus(SubscriptionPlan $subscription_plan): JsonResponse | RedirectResponse {
        try {
            $newStatus                 = $subscription_plan->status === 'active' ? 'inactive' : 'active';
            $subscription_plan->status = $newStatus;

            if ($newStatus === 'inactive' && $subscription_plan->is_recommended) {
                $subscription_plan->is_recommended = false;
            }

            $subscription_plan->save();

            return redirect()->route('subscription-plan.index')->with('t-success', 'Plan status updated successfully.');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle the recommended status of a subscription plan.
     *
     * @param SubscriptionPlan $subscription_plan
     * @return JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function toggleRecommended(SubscriptionPlan $subscription_plan): JsonResponse | RedirectResponse {
        try {
            // If this plan is being set as recommended, first remove recommended status from all plans
            if (!$subscription_plan->is_recommended) {
                SubscriptionPlan::where('is_recommended', true)->update(['is_recommended' => false]);
            }

            // Toggle the recommended status for this plan
            $subscription_plan->is_recommended = !$subscription_plan->is_recommended;
            $subscription_plan->save();

            return redirect()->route('subscription-plan.index')
                ->with('t-success', $subscription_plan->is_recommended ? 'Plan set as popular.' : 'Plan removed from popular.');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update a subscription plan.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function update(Request $request, $id): JsonResponse | RedirectResponse {
        try {
            $plan = SubscriptionPlan::findOrFail($id);

            $validated = $request->validate([
                'plan_type'        => 'required|in:free,paid',
                'name'             => 'required|string',
                'billing_interval' => 'required|string|in:month,year',
                'price'            => 'required|numeric|min:0',
                'currency'         => 'required|string',
                'description'      => 'nullable|string',
                'features'         => 'array',
            ]);

            // Plan type logic
            if ($request->plan_type === 'free') {
                $validated['price'] = 0;
            } else {
                if ($validated['price'] <= 0) {
                    return back()->withErrors(['price' => 'Price must be greater than 0 for paid plans.'])->withInput();
                }
            }

            // Parse features as before
            if (!empty($request->input('features'))) {
                $featuresString        = $request->input('features')[0] ?? '';
                $featuresArray         = array_map('trim', explode(',', $featuresString));
                $validated['features'] = $featuresArray;
            }

            $plan->update($validated);

            return redirect()->route('subscription-plan.index')->with('t-success', 'Plan updated successfully.');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
