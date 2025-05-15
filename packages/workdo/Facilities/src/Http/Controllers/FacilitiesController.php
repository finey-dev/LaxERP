<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\WorkSpace;
use Illuminate\Support\Facades\Auth;
use Workdo\Facilities\Entities\FacilitiesBooking;

class FacilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct()
    {
        if (module_is_active('GoogleAuthentication')) {
            $this->middleware('2fa');
        }
    }
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('facilities dashboard manage')) {

            $user = Auth::user();
            $facilities_book = [];
            $workspace = WorkSpace::where('id', getActiveWorkSpace())->first();

            if (Auth::user()->type == 'company') {
                $current_month_booking = FacilitiesBooking::whereMonth('date', date('m'))
                    ->whereYear('date', date('Y'))
                    ->where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->OrderBy('date', 'ASC')
                    ->get();

                $totalBooking = FacilitiesBooking::where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->count();
                $pendingBooking = FacilitiesBooking::where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->where('stage_id', '=', '0')->count();
                $completeBooking = FacilitiesBooking::where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->where('stage_id', '=', '2')->count();
                $facilitiesbooking = FacilitiesBooking::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

                if (!empty($request->date)) {
                    $date_range = explode(' to ', $request->date);
                    $facilitiesbooking->where('start_date', '>=', $date_range[0]);
                    $facilitiesbooking->where('end_date', '<=', $date_range[1]);
                }

            } else {
                $current_month_booking = FacilitiesBooking::whereMonth('date', date('m'))
                    ->whereYear('date', date('Y'))
                    ->where('client_id', Auth::user()->id)
                    ->where('workspace', getActiveWorkSpace())
                    ->OrderBy('date', 'ASC')
                    ->get();

                $totalBooking = FacilitiesBooking::where('client_id', Auth::user()->id)->where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->count();
                $pendingBooking = FacilitiesBooking::where('client_id', Auth::user()->id)->where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->where('stage_id', '=', '0')->count();
                $completeBooking = FacilitiesBooking::where('client_id', Auth::user()->id)->where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->where('stage_id', '=', '2')->count();
                $facilitiesbooking = FacilitiesBooking::where('client_id', Auth::user()->id)->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

                if (!empty($request->date)) {
                    $date_range = explode(' to ', $request->date);
                    $facilitiesbooking->where('start_date', '>=', $date_range[0]);
                    $facilitiesbooking->where('end_date', '<=', $date_range[1]);
                    $facilitiesbooking->where('client_id', Auth::user()->id);
                }
            }

            $facilitiesbookings = $facilitiesbooking->get();

            foreach ($facilitiesbookings as $key => $facilitiesbooking) {
                $data = [
                    'title' => $facilitiesbooking->client_id != 0 ? (isset($facilitiesbooking->user) ? $facilitiesbooking->user->name : '-') : (isset($facilitiesbooking->name) ? $facilitiesbooking->name : ''),
                    'start' => $facilitiesbooking->date,
                    'end' => $facilitiesbooking->date,
                    'className' => 'event-danger',
                ];
                array_push($facilities_book, $data);
            }

            $format = 'Y-m-d';
            $m = date("m");
            $de = date("d");
            $y = date("Y");

            $arryTemp = [];
            for ($i = 0; $i <= 7 - 1; $i++) {
                $date = date($format, mktime(0, 0, 0, $m, ($de + $i), $y));
                $arryTemp['booking_date'][] = __(date('d-M', strtotime($date)));
                if (Auth::user()->type == 'company') {

                    $arryTemp['bookings'][] = FacilitiesBooking::whereDate('date', $date)->where('workspace', '=', getActiveWorkSpace())->count();
                } else {
                    $arryTemp['bookings'][] = FacilitiesBooking::where('client_id', Auth::user()->id)->whereDate('date', $date)->where('workspace', '=', getActiveWorkSpace())->count();

                }
            }
            $chartcall = $arryTemp;
            $chartcall['booking_date'] = $arryTemp['booking_date'];
            $chartcall['bookings'] = $arryTemp['bookings'];

            return view('facilities::dashboard.index', compact('facilities_book', 'current_month_booking', 'chartcall', 'workspace', 'totalBooking', 'pendingBooking', 'completeBooking'));
        } else {
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
        //
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
