<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Facilities\Entities\FacilitiesWorking;
use Illuminate\Support\Facades\Auth;
use Workdo\Facilities\Events\UpdateFacilitiesHour;

class FacilitiesWorkingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('facilitiesworking manage')) {

            $work = FacilitiesWorking::where('created_by',creatorId())->where('workspace', getActiveWorkSpace())->first();
            $week_days = FacilitiesWorking::$week_days;
            return view('facilities::working.index', compact('work','week_days'));

        }else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('facilities::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('facilitiesworking create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'opening_time' => 'required',
                    'closing_time' => 'required',
                    'working_days' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $facilitiesWorking['opening_time']    =  $request['opening_time'];
            $facilitiesWorking['closing_time']    = $request->input('closing_time');
            $facilitiesWorking['day_of_week']     = implode(',',$request->input('working_days'));
            if(module_is_active('Hrm')){
                $facilitiesWorking['holiday_setting'] = $request->input('holiday_setting');
            }

            $data = [
                'workspace' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ];

            FacilitiesWorking::updateOrInsert($data, $facilitiesWorking);

            event(new UpdateFacilitiesHour($request,$facilitiesWorking));

            return redirect()->route('facilities-working.index')->with('success', __('The working hours has been created successfully.'));

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
        return view('facilities::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('facilities::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
