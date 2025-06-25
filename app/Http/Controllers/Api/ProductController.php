<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Product;
use Exception;

class ProductController extends Controller {
    public function store(StoreProductRequest $request) {
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
}
