<?php

namespace App\Observers\Web\Backend\CMS;

use App\Models\CMS;

class HomePageHeroSectionObserver {
    /**
     * Handle the CMS "creating" event.
     *
     * @param CMS  $cms
     * @return void
     */
    public function creating(CMS $cms): void {
        if ($cms->section === 'hero') {
            $cms->section = 'hero';
        }
    }

    /**
     * Handle the CMS "updating" event.
     *
     * @param CMS  $cms
     * @return void
     */
    public function updating(CMS $cms): void {
        if ($cms->section === 'hero') {
            $cms->section = 'hero';
        }
    }
}
