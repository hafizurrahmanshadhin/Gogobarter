<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Home\FeatureItemResource;
use App\Http\Resources\Api\Home\HeroSectionResource;
use App\Http\Resources\Api\Home\InstructionBannerResource;
use App\Http\Resources\Api\Home\InstructionSectionResource;
use App\Http\Resources\Api\Home\ServiceSectionResource;
use App\Http\Resources\Api\Home\TradingSectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $howItWorks = null;
        if ($this->instruction_banner) {
            $bannerArr  = (new InstructionBannerResource($this->instruction_banner))->toArray($request);
            $howItWorks = array_merge(
                $bannerArr,
                [
                    'steps' => $this->instruction_section
                    ? InstructionSectionResource::collection($this->instruction_section)
                    : [],
                ]
            );
        }

        return [
            'hero_section'    => $this->hero_section ? new HeroSectionResource($this->hero_section) : null,
            'featured_items'  => FeatureItemResource::collection($this->featured_items),
            'service_section' => $this->service_section ? new ServiceSectionResource($this->service_section) : [],
            'how_it_works'    => $howItWorks,
            'trading_section' => $this->trading_section ? new TradingSectionResource($this->trading_section) : null,
        ];
    }
}
