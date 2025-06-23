<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class HomePageInstructionSectionController extends Controller {
    /**
     * Display the instruction section.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse|View
     * @throws Exception
     */
    public function index(Request $request): JsonResponse | RedirectResponse | View {
        try {
            if ($request->ajax()) {
                $data = CMS::where('section', 'instruction')
                    ->select(['id', 'title', 'description', 'status'])
                    ->latest()
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('description', function ($data) {
                        $description      = $data->description;
                        $shortDescription = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                        return '<p>' . $shortDescription . '</p>';
                    })
                    ->addColumn('status', function ($data) {
                        return '
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck' . $data->id . '" ' . ($data->status == 'active' ? 'checked' : '') . ' onclick="showStatusChangeAlert(' . $data->id . ')">
                                </div>
                            </div>
                        ';
                    })
                    ->addColumn('action', function ($data) {
                        return '
                            <div class="d-flex justify-content-center hstack gap-3 fs-base">
                                <a href="javascript:void(0);" onclick="showFeatureDetails(' . $data->id . ')" class="link-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#viewFeatureModal" title="View">
                                    <i class="ri-eye-line" style="font-size: 24px;"></i>
                                </a>

                                <a href="javascript:void(0);" class="link-primary text-decoration-none edit-feature" data-id="' . $data->id . '" title="Edit">
                                    <i class="ri-pencil-line" style="font-size:24px;"></i>
                                </a>
                            </div>
                        ';
                    })
                    ->rawColumns(['description', 'status', 'action'])
                    ->make();
            }
            $instruction = CMS::firstOrNew(['section' => 'instruction_banner']);
            return view('backend.layouts.cms.home-page.instruction-section', compact('instruction'));
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Update instruction section.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(Request $request): RedirectResponse {
        try {
            $validator = Validator::make($request->all(), [
                'title'        => 'required|string',
                'image'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:20480',
                'remove_image' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $instruction = CMS::firstOrNew(['section' => 'instruction_banner']);

            $instruction->title = $request->title;

            //* Handle image file
            if ($request->boolean('remove_image')) {
                if ($instruction->image) {
                    Helper::fileDelete(public_path($instruction->image));
                    $instruction->image = null;
                }
            } elseif ($request->hasFile('image')) {
                if ($instruction->image) {
                    Helper::fileDelete(public_path($instruction->image));
                }
                $instruction->image = Helper::fileUpload($request->file('image'), 'instruction', $instruction->image);
            }
            $instruction->save();

            return redirect()->route('home-page.instruction.index')->with('t-success', 'Instruction section updated successfully.');
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Show the specified instruction details.
     *
     * @param  int  $id
     * @return JsonResponse
     * @throws Exception
     */
    public function show(int $id): JsonResponse {
        try {
            $data = CMS::findOrFail($id);
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified instruction in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     * @throws Exception
     */
    public function updateInstruction(Request $request, int $id): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'title'       => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return Helper::jsonResponse(false, 'Validation errors', 422, null, $validator->errors());
            }

            $instruction              = CMS::findOrFail($id);
            $instruction->title       = $request->title;
            $instruction->description = $request->description;
            $instruction->section     = 'instruction';
            $instruction->save();

            return Helper::jsonResponse(true, 'Data updated successfully.', 200, $instruction);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Error updating: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Change the status of the specified instruction feature.
     *
     * @param  int  $id
     * @return JsonResponse
     * @throws Exception
     */
    public function status(int $id): JsonResponse {
        try {
            $instruction = CMS::findOrFail($id);

            if ($instruction->status === 'active') {
                $instruction->status = 'inactive';
                $instruction->save();

                return Helper::jsonResponse(false, 'Unpublished Successfully.', 200, $instruction);
            } else {
                $instruction->status = 'active';
                $instruction->save();

                return Helper::jsonResponse(true, 'Published Successfully.', 200, $instruction);
            }
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
