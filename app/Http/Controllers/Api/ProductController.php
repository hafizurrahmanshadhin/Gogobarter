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
     * Get the authenticated user's products
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function myProducts(): JsonResponse {
        try {
            $user     = Auth::user();
            $products = $user->products()->with('user')->latest()->get();

            return Helper::jsonResponse(true, 'My products fetched successfully.', 200, FeatureItemResource::collection($products));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
