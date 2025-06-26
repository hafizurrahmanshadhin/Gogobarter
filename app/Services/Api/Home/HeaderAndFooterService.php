<?php

namespace App\Services\Api\Home;

use App\Models\Content;
use App\Models\DynamicPage;
use App\Models\SocialMedia;
use App\Models\SystemSetting;
use Exception;

class HeaderAndFooterService {
    /**
     * Fetch and prepare system setting plus social media data.
     *
     * @return object
     * @throws Exception
     */
    public function getHeaderFooterData(): object {
        try {
            $systemSetting      = SystemSetting::first();
            $socialMedias       = SocialMedia::all();
            $privacyPolicy      = Content::where('type', 'privacyPolicy')->where('status', 'active')->first();
            $termsAndConditions = Content::where('type', 'termsAndConditions')->where('status', 'active')->first();
            $dynamicPages       = DynamicPage::where('status', 'active')->get();

            if (!$systemSetting) {
                throw new Exception('No Data Found');
            }

            // Return an object containing both sets of data
            return (object) [
                'system_setting'       => $systemSetting,
                'social_medias'        => $socialMedias,
                'privacy_policy'       => $privacyPolicy,
                'terms_and_conditions' => $termsAndConditions,
                'dynamic_pages'        => $dynamicPages,
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
