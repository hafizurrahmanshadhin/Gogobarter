<?php

namespace App\Services\Web\Backend\CMS;

use App\Models\CMS;

class HomePageHeroSectionService {
    public function getOrCreateHeroSection(): CMS {
        return CMS::firstOrNew(['section' => 'hero']);
    }

    public function updateHeroSection(array $data): void {
        $heroSection = $this->getOrCreateHeroSection();
        $heroSection->fill($data);
        $heroSection->save();
    }
}
