<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HomePageTradingSectionController extends Controller {
    /**
     * Display the trading section.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(): RedirectResponse | View {
        try {
            $tradingSection = CMS::firstOrNew(['section' => 'trading']);
            return view('backend.layouts.cms.home-page.trading-section', compact('tradingSection'));
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Update the trading section.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'title'       => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $tradingSection = CMS::firstOrNew(['section' => 'trading']);

            $tradingSection->title       = $request->title;
            $tradingSection->description = $request->description;
            $tradingSection->section     = 'trading';
            $tradingSection->save();

            return redirect()->route('home-page.trading.index')->with('t-success', 'Trading section updated successfully.');
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }
}
