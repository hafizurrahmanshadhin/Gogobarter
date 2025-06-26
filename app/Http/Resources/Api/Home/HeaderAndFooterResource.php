<?php

namespace App\Http\Resources\Api\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeaderAndFooterResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $systemSetting      = $this->system_setting;
        $socialMedias       = $this->social_medias;
        $privacyPolicy      = $this->privacy_policy;
        $termsAndConditions = $this->terms_and_conditions;
        $dynamicPages       = $this->dynamic_pages;

        return [
            'logo'                 => $systemSetting->logo ? url($systemSetting->logo) : asset('frontend/Logo.png'),
            'favicon'              => $systemSetting->favicon ? url($systemSetting->favicon) : asset('frontend/icons/success-icon.svg'),
            'description'          => $systemSetting->description,
            'phone_number'         => $systemSetting->phone_number,
            'email'                => $systemSetting->email,
            'address'              => $systemSetting->address,
            'copyright_text'       => $systemSetting->copyright_text,

            // Social Media Links
            'social_media'         => $socialMedias,

            // Privacy Policy
            'privacy_policy'       => $privacyPolicy ? [
                'title'   => $privacyPolicy->title,
                'slug'    => $privacyPolicy->slug,
                'content' => $privacyPolicy->content,
            ] : null,

            // Terms and Conditions
            'terms_and_conditions' => $termsAndConditions ? [
                'title'   => $termsAndConditions->title,
                'slug'    => $termsAndConditions->slug,
                'content' => $termsAndConditions->content,
            ] : null,

            // Dynamic Pages
            'dynamic_pages'        => $dynamicPages ? $dynamicPages->map(function ($page) {
                return [
                    'id'      => $page->id,
                    'title'   => $page->page_title,
                    'slug'    => $page->page_slug,
                    'content' => $page->page_content,
                ];
            }) : [],
        ];
    }
}
