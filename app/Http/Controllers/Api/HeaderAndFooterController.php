<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Home\HeaderAndFooterResource;
use App\Services\Api\Home\HeaderAndFooterService;
use Exception;
use Illuminate\Http\JsonResponse;

class HeaderAndFooterController extends Controller {
    private HeaderAndFooterService $headerFooterService;
    public function __construct(HeaderAndFooterService $headerFooterService) {
        $this->headerFooterService = $headerFooterService;
    }

    /**
     * Single action: Fetch the header & footer data, transform, and return it.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(): JsonResponse {
        try {
            // Get the data from the service.
            $data = $this->headerFooterService->getHeaderFooterData();

            // Pass it to the resource.
            $resource = new HeaderAndFooterResource($data);

            return Helper::jsonResponse(true, 'Data Fetched Successfully', 200, $resource);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
