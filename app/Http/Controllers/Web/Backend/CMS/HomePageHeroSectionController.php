<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Backend\CMS\HomePageHeroSectionRequest;
use App\Services\Web\Backend\CMS\HomePageHeroSectionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomePageHeroSectionController extends Controller {
    protected HomePageHeroSectionService $heroSectionService;

    public function __construct(HomePageHeroSectionService $heroSectionService) {
        $this->heroSectionService = $heroSectionService;
    }

    /**
     * Display the hero section of the home page.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(): RedirectResponse | View {
        try {
            $heroSection = $this->heroSectionService->getOrCreateHeroSection();
            return view('backend.layouts.cms.home-page.hero-section', compact('heroSection'));
        } catch (Exception $e) {
            return redirect()->route('home-page.hero-section.index')->with('t-error', $e->getMessage());
        }
    }

    /**
     * Update the hero section of the home page.
     *
     * @param HomePageHeroSectionRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(HomePageHeroSectionRequest $request): RedirectResponse {
        try {
            $this->heroSectionService->updateHeroSection($request->validated());
            return redirect()->route('home-page.hero-section.index')->with('t-success', 'Hero Section Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->route('home-page.hero-section.index')->with('t-error', $e->getMessage());
        }
    }
}
