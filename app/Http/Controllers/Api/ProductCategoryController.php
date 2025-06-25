<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Product\ProductCategoryResource;
use App\Models\ProductCategory;
use Exception;

class ProductCategoryController extends Controller {
    public function index() {
        try {
            $categories = ProductCategory::where('status', 'active')->get();
            return Helper::jsonResponse(true, 'Category list fetched successfully.', 200, ProductCategoryResource::collection($categories));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
