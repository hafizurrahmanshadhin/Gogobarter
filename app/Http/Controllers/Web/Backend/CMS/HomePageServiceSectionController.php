<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageServiceSectionController extends Controller {
    public function index() {
        $serviceSection = CMS::firstOrNew(['section' => 'service']);
        $items          = $serviceSection->items ?? [];

        for ($i = 0; $i < 3; $i++) {
            if (!isset($items[$i])) {
                $items[$i] = [
                    'title'       => '',
                    'description' => '',
                    'image'       => null,
                ];
            } else {
                $items[$i]['title']       = $items[$i]['title'] ?? '';
                $items[$i]['description'] = $items[$i]['description'] ?? '';
                $items[$i]['image']       = $items[$i]['image'] ?? null;
            }
        }

        return view('backend.layouts.cms.home-page.service-section', compact('serviceSection', 'items'));
    }

    public function update(Request $request) {
        $rules = [
            'titles'     => 'required|array|size:3',
            'titles.*'   => 'required|string|max:255',
            'contents'   => 'required|array|size:3',
            'contents.*' => 'required|string',
            'images'     => 'nullable|array|size:3',
            'images.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $messages = [
            'titles.*.required'   => 'Each service item needs a title.',
            'contents.*.required' => 'Each service item needs a description.',
            'images.*.image'      => 'Each file must be a valid image (jpeg/png/gif).',
            'images.*.max'        => 'Each image must be smaller than 2 MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $serviceSection = CMS::firstOrNew(['section' => 'service']);
        $oldItems       = $serviceSection->items ?? [];
        $newItems       = [];

        for ($i = 0; $i < 3; $i++) {
            $title       = $request->input("titles.$i");
            $description = $request->input("contents.$i");

            $existingImagePath = $oldItems[$i]['image'] ?? null;
            $newImagePath      = null;

            if ($request->hasFile("images.$i")) {
                $file     = $request->file("images.$i");
                $uploaded = Helper::fileUpload($file, 'service', $title);

                if ($uploaded) {
                    if ($existingImagePath) {
                        Helper::fileDelete(public_path($existingImagePath));
                    }
                    $newImagePath = $uploaded;
                } else {
                    $newImagePath = $existingImagePath;
                }
            } else {
                $newImagePath = $existingImagePath;
            }

            $newItems[] = [
                'title'       => $title,
                'description' => $description,
                'image'       => $newImagePath,
            ];
        }

        $serviceSection->section = 'service';
        $serviceSection->items   = $newItems;
        $serviceSection->save();

        return redirect()->route('home-page.service.index')->with('t-success', 'Service Section updated successfully.');
    }
}
