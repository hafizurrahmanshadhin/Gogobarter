<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreExchangeRequest;
use App\Http\Resources\Api\ExchangeRequestResource;
use App\Http\Resources\Api\OfferedProductDetailsResource;
use App\Models\ExchangeRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRequestController extends Controller {
    /**
     * Store a new exchange request.
     *
     * @param StoreExchangeRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreExchangeRequest $request): JsonResponse {
        try {
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = Helper::fileUpload($file, 'products');
                    if ($path) {
                        $imagePaths[] = $path;
                    }
                }
            }

            $offeredProduct = Product::create([
                'user_id'             => Auth::id(),
                'product_category_id' => $request->product_category_id,
                'name'                => $request->name,
                'images'              => $imagePaths,
                'description'         => $request->description,
                'condition'           => $request->condition,
            ]);

            $exchangeRequest = ExchangeRequest::create([
                'requester_id'         => Auth::id(),
                'requested_product_id' => $request->requested_product_id,
                'offered_product_id'   => $offeredProduct->id,
                'message'              => $request->message,
                'status'               => 'pending',
            ]);

            return Helper::jsonResponse(true, 'Exchange request sent successfully.', 201, $exchangeRequest);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to send exchange request.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get exchange requests made by the user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function myExchangeRequests(Request $request): JsonResponse {
        try {
            $user   = Auth::user();
            $status = $request->query('status', 'pending');

            $requests = ExchangeRequest::with([
                'requester',
                'requestedProduct',
                'offeredProduct',
            ])
                ->whereHas('requestedProduct', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', $status)
                ->latest()
                ->get();

            return Helper::jsonResponse(
                true,
                ucfirst($status) . ' exchange requests fetched successfully.',
                200,
                ExchangeRequestResource::collection($requests)
            );
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch exchange requests.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Accept an exchange request.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function acceptRequest($id): JsonResponse {
        try {
            $user    = Auth::user();
            $request = ExchangeRequest::where('id', $id)
                ->whereHas('requestedProduct', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', 'pending')
                ->firstOrFail();

            $request->status = 'accepted';
            $request->save();

            return Helper::jsonResponse(true, 'Exchange request accepted.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to accept exchange request.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Decline an exchange request.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function declineRequest($id): JsonResponse {
        try {
            $user    = Auth::user();
            $request = ExchangeRequest::where('id', $id)
                ->whereHas('requestedProduct', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', 'pending')
                ->firstOrFail();

            $request->status = 'rejected';
            $request->save();

            return Helper::jsonResponse(true, 'Exchange request declined.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to decline exchange request.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get details of the offered product in an exchange request.
     *
     * @param int $exchangeRequestId
     * @return JsonResponse
     * @throws Exception
     */
    public function offeredProductDetails($exchangeRequestId): JsonResponse {
        try {
            $user = Auth::user();

            $exchangeRequest = ExchangeRequest::with(['offeredProduct.user', 'offeredProduct.category'])
                ->where('id', $exchangeRequestId)
                ->whereHas('requestedProduct', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->firstOrFail();

            $offeredProduct                  = $exchangeRequest->offeredProduct;
            $offeredProduct->exchangeRequest = $exchangeRequest;

            return Helper::jsonResponse(true, 'Offered product details fetched successfully.', 200,
                new OfferedProductDetailsResource($offeredProduct)
            );
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch offered product details.', 500, null, ['error' => $e->getMessage()]);
        }
    }
}
