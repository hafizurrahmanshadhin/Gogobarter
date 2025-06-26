<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HomeResource;
use App\Models\CMS;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller {
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
}
