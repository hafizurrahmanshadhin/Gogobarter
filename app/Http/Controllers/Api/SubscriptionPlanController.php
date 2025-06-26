<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SubscriptionPlan\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Exception;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller {
    /**
     * Display a listing of subscription plans.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(): JsonResponse {
        try {
            $categories = SubscriptionPlan::where('status', 'active')->get();
            return Helper::jsonResponse(true, 'Subscription plan fetched successfully.', 200, SubscriptionPlanResource::collection($categories));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
