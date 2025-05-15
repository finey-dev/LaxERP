<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\DataTables\RfxApplicantDataTable;
use Workdo\Procurement\Entities\RfxApplicant;
use Workdo\Procurement\Events\CreateRfxApplicant;
use Illuminate\Support\Facades\Crypt;
use Workdo\Procurement\Events\DestroyRfxApplicant;
use Workdo\Procurement\Events\UpdateRfxApplicant;

class RfxApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RfxApplicantDataTable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('rfx applicant manage')) {
            $rfx_applicant_country = RfxApplicant::distinct()->pluck('country', 'country');
            $rfx_applicant_country->prepend('All', '');

            $rfx_applicant_state = RfxApplicant::distinct()->pluck('state', 'state');
            $rfx_applicant_state->prepend('All', '');

            $filter = [
                'name' => isset($request->name) ? $request->name : '',
                'gender' => isset($request->gender) ? $request->gender : '',
                'country' => isset($request->country) ? $request->country : '',
                'state' => isset($request->state) ? $request->state : '',
            ];
            return $dataTable->render('procurement::rfxApplicant.index', compact('filter', 'rfx_applicant_country', 'rfx_applicant_state'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('rfx applicant create')) {

            return view('procurement::rfxApplicant.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('rfx applicant create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|max:120',
                    'dob' => 'before:' . date('Y-m-d'),
                    'phone' => 'required|max:15',
                    'gender' => 'required|max:8',
                    'country' => 'required|max:50',
                    'state' => 'required|max:50',
                    'city' => 'required|max:50',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if (!empty($request->profile)) {

                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $upload = upload_file($request, 'profile', $fileNameToStore, 'RfxApplication');
                if ($upload['flag'] == 1) {
                    $url = $upload['url'];
                } else {
                    return redirect()->back()->with('error', $upload['msg']);
                }
            }

            if (!empty($request->proposal)) {

                $filenameWithExt = $request->file('proposal')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('proposal')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $upload = upload_file($request, 'proposal', $fileNameToStore, 'RfxApplication');
                if ($upload['flag'] == 1) {
                    $url1 = $upload['url'];
                } else {
                    return redirect()->back()->with('error', $upload['msg']);
                }
            }

            $rfx_applicant = new RfxApplicant();
            $rfx_applicant->name = $request->name;
            $rfx_applicant->email = $request->email;
            $rfx_applicant->phone = $request->phone;
            $rfx_applicant->dob = $request->dob;
            $rfx_applicant->gender = $request->gender;
            $rfx_applicant->country = $request->country;
            $rfx_applicant->state = $request->state;
            $rfx_applicant->city = $request->city;
            $rfx_applicant->description = $request->description;
            $rfx_applicant->profile = !empty($request->profile) ? $url : '';
            $rfx_applicant->proposal = !empty($request->proposal) ? $url1 : '';
            $rfx_applicant->workspace = getActiveWorkSpace();
            $rfx_applicant->created_by = creatorId();
            $rfx_applicant->save();

            event(new CreateRfxApplicant($request, $rfx_applicant));

            return redirect()->route('rfx-applicant.index')->with('success', __('The rfx applicant has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Page Not Found.'));
        }
        if (Auth::user()->isAbleTo('rfx applicant edit')) {
            $rfx_applicants = RfxApplicant::find($id);
            if ($rfx_applicants) {
                return view('procurement::rfxApplicant.edit', compact('rfx_applicants'));
            } else {
                return response()->json(['error' => __('The rfx applicant is not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('rfx applicant edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|max:120',
                    'phone' => 'required|max:15',
                    'dob' => 'before:' . date('Y-m-d'),
                    'gender' => 'required|max:8',
                    'country' => 'required|max:50',
                    'state' => 'required|max:50',
                    'city' => 'required|max:50',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfx_applicant = RfxApplicant::find($id);
            if ($rfx_applicant) {
                if (!empty($request->profile)) {
                    $filenameWithExt = $request->file('profile')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('profile')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $upload = upload_file($request, 'profile', $fileNameToStore, 'RfxApplication');
                    if ($upload['flag'] == 1) {
                        if (!empty($rfx_applicant->profile)) {
                            delete_file($rfx_applicant->profile);
                        }
                        $url = $upload['url'];
                        $rfx_applicant->profile = $url;
                    } else {
                        return redirect()->back()->with('error', $upload['msg']);
                    }
                }

                if (!empty($request->proposal)) {
                    $filenameWithExt = $request->file('proposal')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('proposal')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $upload = upload_file($request, 'proposal', $fileNameToStore, 'RfxApplication');
                    if ($upload['flag'] == 1) {
                        if (!empty($rfx_applicant->proposal)) {
                            delete_file($rfx_applicant->proposal);
                        }
                        $url1 = $upload['url'];
                        $rfx_applicant->proposal = $url1;
                    } else {
                        return redirect()->back()->with('error', $upload['msg']);
                    }
                }

                $rfx_applicant->name = $request->name;
                $rfx_applicant->email = $request->email;
                $rfx_applicant->phone = $request->phone;
                $rfx_applicant->dob = $request->dob;
                $rfx_applicant->gender = $request->gender;
                $rfx_applicant->country = $request->country;
                $rfx_applicant->state = $request->state;
                $rfx_applicant->city = $request->city;
                $rfx_applicant->workspace = getActiveWorkSpace();
                $rfx_applicant->created_by = creatorId();
                $rfx_applicant->save();

                event(new UpdateRfxApplicant($request, $rfx_applicant));

                return redirect()->route('rfx-applicant.index')->with('success', __('The rfx applicant are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The rfx applicant is not found.'));
            }


        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('rfx applicant delete')) {
            $currentWorkspace = getActiveWorkSpace();
            $rfx_applicant = RfxApplicant::find($id);
            if ($rfx_applicant) {
                if ($rfx_applicant->created_by == creatorId() && $rfx_applicant->workspace == $currentWorkspace) {

                    event(new DestroyRfxApplicant($rfx_applicant));

                    if (!empty($rfx_applicant->profile)) {
                        delete_file($rfx_applicant->profile);
                    }

                    if (!empty($rfx_applicant->resume)) {
                        delete_file($rfx_applicant->resume);
                    }

                    $rfx_applicant->delete();
                    return redirect()->back()->with('success', __('The rfx applicant has been deleted.'));
                } else {
                    return redirect()->back()->with('error', 'Permission denied.');
                }
            } else {
                return redirect()->back()->with('error', __('The rfx applicant is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
