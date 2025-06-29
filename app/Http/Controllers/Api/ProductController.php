<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\Api\Home\FeatureItemResource;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Product;
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
                'user_id'             => $request->user()->id,
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

            // Handle images if provided
            if ($request->hasFile('images')) {
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
