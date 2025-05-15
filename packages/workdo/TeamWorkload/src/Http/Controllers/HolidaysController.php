<?php

namespace Workdo\TeamWorkload\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\TeamWorkload\DataTables\HolidayDataTable;
use Workdo\TeamWorkload\Entities\Holiday;
use Workdo\TeamWorkload\Events\CreateWorloadHolidays;

class HolidaysController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(HolidayDataTable $dataTable, Request $request)
    {


        if (Auth::user()->isAbleTo('workload holidays manage'))
        {
            return $dataTable->render('team-workload::holiday.index'    );

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

        if (Auth::user()->isAbleTo('workload holidays create'))
        {
            return view('team-workload::holiday.create');
        }
        else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('workload holidays create'))
        {
            $validator = \Validator::make(
                $request->all(),
                [
                    'occasion' => 'required',
                    'start_date' => 'required|after:yesterday',
                    'end_date' => 'required|after_or_equal:start_date',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $holiday                    = new Holiday();
            $holiday->occasion          = $request->occasion;
            $holiday->start_date        = $request->start_date;
            $holiday->end_date          = $request->end_date;
            $holiday->workspace         = getActiveWorkSpace();
            $holiday->created_by        = creatorId();
            $holiday->save();

            event(new CreateWorloadHolidays($request,$holiday));

            return redirect()->route('holidays.index')->with('success','Holiday successfully created.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($id)
    {
        return redirect()->back();
        return view('team-workload::show');

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Holiday $holiday)
    {

        if (Auth::user()->isAbleTo('workload holidays edit'))
        {
            return view('team-workload::holiday.edit', compact('holiday'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Holiday $holiday)
    {
        if (\Auth::user()->isAbleTo('workload holidays edit'))
        {
            $validator = \Validator::make(
                            $request->all(),
                            [
                                'occasion' => 'required',
                                'start_date' => 'required|date',
                                'end_date' => 'required|after_or_equal:start_date',
                            ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $holiday->occasion          = $request->occasion;
            $holiday->start_date        = $request->start_date;
            $holiday->end_date          = $request->end_date;
            $holiday->save();

            return redirect()->route('holidays.index')->with('success','Holiday successfully updated.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Holiday $holiday)
    {
        if (Auth::user()->isAbleTo('workload holidays delete'))
        {

            $holiday->delete();
            return redirect()->route('holidays.index')->with('success','Holiday successfully deleted.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function calenders(Request $request)
    {

        if (Auth::user()->isAbleTo('workload holidays manage'))
        {
            $holidays = Holiday::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace());
            $today_date = date('m');
            $current_month_event = Holiday::select( 'occasion','start_date','end_date', 'created_at')->where('workspace',getActiveWorkSpace())
                                    ->whereRaw('MONTH(start_date) = ? AND MONTH(end_date) = ?', [date('m'),date('m')])
                                    ->get();
            if (!empty($request->start_date))
            {
                $holidays->where('start_date', '>=', $request->start_date);
            }
            if (!empty($request->end_date)) {
                $holidays->where('end_date', '<=', $request->end_date);
            }
            $holidays = $holidays->get();

            $arrHolidays = [];

            foreach ($holidays as $holiday)
            {
                $arr['id']        = $holiday['id'];
                $arr['title']     = $holiday['occasion'];
                $arr['start']     = $holiday['start_date'];
                $arr['end']       = date('Y-m-d', strtotime($holiday['end_date'] . ' +1 day'));
                $arr['className'] = 'event-danger holiday-edit';
                $arr['url']       = route('holidays.edit', $holiday['id']);
                $arrHolidays[]    = $arr;

            }

            $arrHolidays =  json_encode($arrHolidays);
            return view('team-workload::holiday.calender', compact('arrHolidays','current_month_event'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



}
