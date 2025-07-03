<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\Api\Home\FeatureItemResource;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller {
    /**
     * Store a new product
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreProductRequest $request): JsonResponse {
        try {
            $user = $request->user();

            // 1. Get the user's current subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                ->orderByDesc('starts_at')
                ->first();

            // 2. If no subscription, deny
            if (!$subscription) {
                return Helper::jsonResponse(false, 'You must purchase a subscription plan to post a product.', 403);
            }

            // 3. If subscription expired, migrate to free plan
            if ($subscription->ends_at && now()->greaterThan($subscription->ends_at)) {
                $subscription->status = 'expired';
                $subscription->save();

                $freePlan = SubscriptionPlan::where('price', 0)->first();
                if ($freePlan) {
                    UserSubscription::create([
                        'user_id'              => $user->id,
                        'subscription_plan_id' => $freePlan->id,
                        'status'               => 'active',
                        'starts_at'            => now(),
                        'ends_at'              => null,
                        'amount_paid'          => 0,
                        'payment_method'       => null,
                        'transaction_id'       => null,
                    ]);
                }

                return Helper::jsonResponse(false, 'Your subscription expired. You have been moved to the free plan. Please purchase a plan to post products.', 403);
            }

            // 4. Check post limit from plan features
            $plan      = $subscription->subscriptionPlan;
            $features  = $plan->features ?? [];
            $postLimit = null; // null means unlimited

            // Find the post limit feature
            foreach ($features as $feature) {
                if (preg_match('/^(\d+)\s*Post/i', $feature, $matches)) {
                    $postLimit = (int) $matches[1];
                    break;
                }
                if (preg_match('/Up to\s*(\d+)\s*Posts?/i', $feature, $matches)) {
                    $postLimit = (int) $matches[1];
                    break;
                }
                if (stripos($feature, 'Unlimited Posts') !== false) {
                    $postLimit = null;
                    break;
                }
            }

            // Count user's current products
            $userProductCount = $user->products()->count();

            if ($postLimit !== null && $userProductCount >= $postLimit) {
                return Helper::jsonResponse(false, "You have reached your post limit for the {$plan->name} plan.", 403);
            }

            // 5. If all checks pass, allow product creation
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = Helper::fileUpload($file, 'products');
                    if ($path) {
                        $imagePaths[] = $path;
                    }
                }
            }

            $product = Product::create([
                'user_id'             => $user->id,
                'product_category_id' => $request->product_category_id,
                'name'                => $request->name,
                'images'              => $imagePaths,
                'description'         => $request->description,
                'condition'           => $request->condition,
            ]);

            return Helper::jsonResponse(true, 'Product created successfully.', 201, new ProductResource($product));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to create product.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing product
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdateProductRequest $request, $id): JsonResponse {
        try {
            $product = Product::where('user_id', $request->user()->id)->findOrFail($id);

            $data = $request->only(['name', 'product_category_id', 'description', 'condition']);

            if ($request->hasFile('images')) {
                if (!empty($product->images) && is_array($product->images)) {
                    foreach ($product->images as $oldImage) {
                        Helper::fileDelete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $file) {
                    $path = Helper::fileUpload($file, 'products');
                    if ($path) {
                        $imagePaths[] = $path;
                    }
                }
                $data['images'] = $imagePaths;
            }

            $product->update($data);

            return Helper::jsonResponse(true, 'Product updated successfully.', 200, new ProductResource($product));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to update product.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a product owned by the authenticated user and its images from storage
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id): JsonResponse {
        try {
            $user    = Auth::user();
            $product = Product::where('user_id', $user->id)->findOrFail($id);

            if (!empty($product->images) && is_array($product->images)) {
                foreach ($product->images as $imagePath) {
                    Helper::fileDelete($imagePath);
                }
            }

            $product->delete();

            return Helper::jsonResponse(true, 'Product and its images deleted successfully.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to delete product.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the authenticated user's products
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function myProducts(): JsonResponse {
        try {
            $user     = Auth::user();
            $products = $user->products()->with('user')->latest()->paginate(8);

            return Helper::jsonResponse(true, 'My products fetched successfully.', 200, [
                'products'   => FeatureItemResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                ],
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle favorite status for a product
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function toggleFavorite(int $id): JsonResponse {
        try {
            $user    = Auth::user();
            $product = Product::where('status', 'active')->findOrFail($id);

            $isFavorite = $user->favorites()->where('product_id', $id)->exists();

            if ($isFavorite) {
                $user->favorites()->detach($id);
                $message  = 'Product removed from favorites.';
                $favorite = false;
            } else {
                $user->favorites()->attach($id);
                $message  = 'Product added to favorites.';
                $favorite = true;
            }

            return Helper::jsonResponse(true, $message, 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the list of favorite products for the authenticated user
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function favoriteList(): JsonResponse {
        try {
            $user      = Auth::user();
            $favorites = $user->favorites()->with('user')->latest()->paginate(8);

            return Helper::jsonResponse(true, 'Favorite products fetched successfully.', 200, [
                'products'   => FeatureItemResource::collection($favorites),
                'pagination' => [
                    'current_page' => $favorites->currentPage(),
                    'last_page'    => $favorites->lastPage(),
                    'per_page'     => $favorites->perPage(),
                    'total'        => $favorites->total(),
                ],
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch favorites.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
