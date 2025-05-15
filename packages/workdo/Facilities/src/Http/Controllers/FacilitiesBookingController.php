<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Facilities\Entities\FacilitiesBooking;
use Workdo\ProductService\Entities\ProductService;
use Illuminate\Support\Facades\Auth;
use Workdo\Facilities\Entities\FacilitiesWorking;
use Workdo\Facilities\Entities\FacilitiesService;
use Workdo\Facilities\Entities\FacilitiesReceipt;
use Workdo\Hrm\Entities\Holiday;
use Carbon\Carbon;
use App\Models\User;
use Workdo\Facilities\Events\CreateFacilitiesBooking;
use Workdo\Facilities\Events\UpdateFacilitiesBooking;
use Workdo\Facilities\Events\DestroyFacilitiesBooking;
use Workdo\Facilities\DataTables\BookingsDataTable;

class FacilitiesBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(BookingsDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('facilitiesbooking manage')) {

            return $dataTable->render('facilities::booking.index');
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
        if (Auth::user()->isAbleTo('facilitiesbooking create')){
            $service = ProductService::where('type','facilities')->where('created_by',creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name','id');
            $service->prepend('select service' , null);
            return view('facilities::booking.create',compact('service'));
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
        if(Auth::user()->isAbleTo('facilitiesbooking create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'service' => 'required',
                    'date' => 'required',
                    'gender' => 'required',
                    'person' => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('facility-booking.index')->with('error', $messages->first());
            }

            $facilitiesBooking                  = new FacilitiesBooking();
            $facilitiesBooking->name            = $request->name;
            $facilitiesBooking->client_id       = $request->client_name ?? '';
            $facilitiesBooking->service         = $request->service ?? '';
            $facilitiesBooking->date            = $request->date ?? '';
            $facilitiesBooking->number          = $request->number ?? $request->client_number;
            $facilitiesBooking->email           = $request->email ?? $request->client_email;
            $facilitiesBooking->gender          = $request->gender?? '';
            $facilitiesBooking->start_time      = $request->start_time ?? '';
            $facilitiesBooking->end_time        = $request->end_time ?? '';
            $facilitiesBooking->person          = $request->person ?? '';
            $facilitiesBooking->workspace       = getActiveWorkSpace();
            $facilitiesBooking->created_by      = creatorId();
            $facilitiesBooking->save();

            event(new CreateFacilitiesBooking($request,$facilitiesBooking));

            return redirect()->route('facility-booking.index')->with('success', __('The booking has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if(Auth::user()->isAbleTo('facilitiesbooking edit'))
        {
            $booking  = FacilitiesBooking::where('id', $id)->where('workspace', getActiveWorkSpace())->first();
            $service = ProductService::where('type','facilities')->where('created_by',creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name','id');

            return view('facilities::booking.edit',compact('booking','service'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if(Auth::user()->isAbleTo('facilitiesbooking edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'service' => 'required',
                                'date' => 'required',
                                'gender' => 'required',
                                'person' => 'required',
                                'start_time' => 'required',
                                'end_time' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $facilitiesBooking                  = FacilitiesBooking::find($id);
            $facilitiesBooking->name            = $request->name ?? '';
            $facilitiesBooking->client_id       = $request->client_name ?? '';
            $facilitiesBooking->service         = $request->service ?? '';
            $facilitiesBooking->date            = $request->date ?? '';
            $facilitiesBooking->number          = $request->number ?? '';
            $facilitiesBooking->email           = $request->email ?? '';
            $facilitiesBooking->gender          = $request->gender?? '';
            $facilitiesBooking->start_time      = $request->start_time ?? '';
            $facilitiesBooking->end_time        = $request->end_time ?? '';
            $facilitiesBooking->person          = $request->person ?? '';
            $facilitiesBooking->workspace       = getActiveWorkSpace();
            $facilitiesBooking->created_by      = creatorId();
            $facilitiesBooking->update();

            event(new UpdateFacilitiesBooking($request,$facilitiesBooking));

            return redirect()->route('facility-booking.index')->with('success', __('The booking has been updated successfully.'));
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
    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('facilitiesbooking delete'))
        {
            $facilitiesBooking = FacilitiesBooking::find($id);

            if(!empty($facilitiesBooking)) {
                FacilitiesReceipt::where('booking_id' , $id)->delete();
                $facilitiesBooking->delete();
                event(new DestroyFacilitiesBooking($facilitiesBooking));

                return redirect()->route('facility-booking.index')->with('success', 'The booking has been deleted.');
            }
        }
        else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function searchBooking(Request $request)
    {
        $returnHTML = '';
        $formattedSelectedDate = $request->date;
        $gender = $request->gender;
        $person = $request->person;

        $facilities_working = FacilitiesWorking::where('workspace',getActiveWorkSpace())->first();

        if (!empty($facilities_working)) {
            if ($facilities_working->holiday_setting == 'on') {
                $holidays = Holiday::where('workspace',getActiveWorkSpace())
                    ->where('start_date', '<=', $formattedSelectedDate)
                    ->where('end_date', '>=', $formattedSelectedDate)
                    ->exists();
                if ($holidays) {
                    $response = [
                        'is_success' => false,
                        'message' => __('Selected date is a holiday. Please choose another date.'),
                        'html' => null,
                    ];

                    return $response;
                }
            }

            $date = Carbon::parse($formattedSelectedDate);
            $day = $date->format('l');

            if (strpos($facilities_working->day_of_week, $day) == false) {
                $response = [
                    'is_success' => false,
                    'message' => __('Selected date is a day off week. Please choose another date.'),
                    'html' => null,
                ];

                return $response;
            }

            $service = FacilitiesService::where('item_id', $request->service)->where('workspace', getActiveWorkSpace())->first();

            $total_working_time = date('G:i', strtotime($facilities_working->closing_time) - strtotime($facilities_working->opening_time));
            $service_time = $service->time;

            $total_working_minutes = $this->convertToMinutes($total_working_time);
            $service_minutes = $this->convertToMinutes($service_time);

            $slots_in_one_day = floor($total_working_minutes / $service_minutes);

            $slot = [];

            $start_time = strtotime($facilities_working->opening_time);
            $service_timess = strtotime($service_time);


            $breck_start = $facilities_working->breck_start;
            $breck_end = $facilities_working->breck_end;

            for ($i = 0; $i < $slots_in_one_day; $i++) {
                $end_time = $start_time + $service_timess;

                $formatted_start_time = date('H:i:s', $start_time);
                $formatted_end_time = date('H:i:s', $end_time);

                $is_slot_booked = FacilitiesBooking::where(function ($query) use ($formatted_start_time, $formatted_end_time , $request) {
                    $query->whereTime('start_time', '>=', $formatted_start_time)
                        ->whereTime('end_time', '<=', $formatted_end_time)
                        ->where('service' , $request->service)
                        ->whereDate('date', $request->date);
                })
                ->count();

                $s = date('G:i', $start_time);
                $e = date('G:i', $end_time);

                if ($is_slot_booked >= $service->slot) {
                    $slot[] = [
                        'start_time' => $s,
                        'end_time' => $e,
                        'available' => false,
                    ];
                } else {
                    $slot[] = [
                        'start_time' => $s,
                        'end_time' => $e,
                        'available' => true,
                    ];
                }

                $start_time = $end_time;
            }
            $booking = null;
            if($request->booking_id)
            {
                $booking = FacilitiesBooking::find($request->booking_id);
            }

            $type = [
                'walk-in' => 'Walk-in',
                'client'  => 'Client',
                'tenant'  => 'tenant',
            ];

            $client = User::where('type', 'client')->where('created_by', creatorId())->where('workspace_id', '=', getActiveWorkSpace())->get()->pluck('name','id');

            $price = $service->item->sale_price * $request->person;

            $returnHTML = view('facilities::booking.slot', compact('service', 'slot', 'booking','gender','person','formattedSelectedDate','type','client','price'))->render();

            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        }
        else
        {
            $response = [
                'is_success' => false,
                'message' => __('First you have to add working hours'),
                'html' => null,
            ];
            return $response;
        }
    }

    public function convertToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }

    public function usersDetail(Request $request)
    {
        $data = User::find($request->user_id);
        return response()->json($data);
    }

    public function users(Request $request)
    {
        if($request->type == 'tenant') {
            $users = User::where('type', 'tenant')->where('created_by', creatorId())->where('workspace_id', '=', getActiveWorkSpace())->get()->pluck('name','id');
        }
        else {
            $users = User::where('type', 'client')->where('created_by', creatorId())->where('workspace_id', '=', getActiveWorkSpace())->get()->pluck('name','id');
        }
        return response()->json($users);
    }
}
