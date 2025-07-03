<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HomeResource;
use App\Http\Resources\Api\Home\FeatureItemResource;
use App\Http\Resources\Api\Product\ProductDetailsResource;
use App\Models\CMS;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller {
    /**
     * Display the home page data including hero section, featured items, service section, instruction banner,
     * instruction section, and trading section.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(): JsonResponse {
        try {
            $products = Product::with('user')->where('status', 'active')->paginate(12);

            $heroSection        = CMS::where('section', 'hero')->where('status', 'active')->first();
            $serviceSection     = CMS::where('section', 'service')->where('status', 'active')->first();
            $instructionBanner  = CMS::where('section', 'instruction_banner')->where('status', 'active')->first();
            $instructionSection = CMS::where('section', 'instruction')->where('status', 'active')->get();
            $tradingSection     = CMS::where('section', 'trading')->where('status', 'active')->first();

            $data = [
                'hero_section'        => $heroSection,
                'featured_items'      => $products,
                'service_section'     => $serviceSection,
                'instruction_banner'  => $instructionBanner,
                'instruction_section' => $instructionSection,
                'trading_section'     => $tradingSection,
            ];

            return Helper::jsonResponse(true, 'Data fetched successfully.', 200, new HomeResource((object) $data));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the details of a specific product.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function show($id): JsonResponse {
        try {
            $product = Product::with('user')->findOrFail($id);

            // Get suggested products from the same category, excluding the current product
            $suggested = Product::with('user')
                ->where('product_category_id', $product->product_category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->limit(8)
                ->get();

            return Helper::jsonResponse(true, 'Product details fetched successfully.', 200, [
                'product'   => new ProductDetailsResource($product),
                'suggested' => FeatureItemResource::collection($suggested),
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a shareable link for a product.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function share($id): JsonResponse {
        try {
            $product = Product::findOrFail($id);

            $shareUrl = config('app.frontend_url') . '/product/' . $product->id;

            return Helper::jsonResponse(true, 'Shareable link generated.', 200, [
                'share_url'   => $shareUrl,
                'title'       => $product->name,
                'description' => $product->description,
                'image'       => is_array($product->images) && count($product->images) > 0 ? $product->images[0] : null,
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to generate share link.', 500, null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Search for products based on the query provided in the request.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function search(Request $request): JsonResponse {
        try {
            $query = $request->input('product');

            $products = Product::with('user')
                ->where('status', 'active')
                ->where('name', 'like', '%' . $query . '%')
                ->latest()
                ->paginate(12);

            return Helper::jsonResponse(true, 'Search results.', 200, [
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
     * Filter products by category.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function filterByCategory(Request $request): JsonResponse {
        try {
            $categoryId = $request->input('product_category_id');

            $products = Product::with('user')
                ->where('status', 'active')
                ->where('product_category_id', $categoryId)
                ->latest()
                ->paginate(12);

            return Helper::jsonResponse(true, 'Filtered products fetched successfully.', 200, [
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
}
