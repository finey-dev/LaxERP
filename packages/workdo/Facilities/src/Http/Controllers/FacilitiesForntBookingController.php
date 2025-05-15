<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\WorkSpace;
use Workdo\Facilities\Entities\FacilitiesBooking;
use Workdo\Facilities\Entities\FacilitiesWorking;
use Workdo\Hrm\Entities\Holiday;
use Carbon\Carbon;
use Workdo\Facilities\Entities\FacilitiesService;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Facilities\Events\CreateFacilitiesBooking;

class FacilitiesForntBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($slug, $lang = null)
    {
        $workspace = WorkSpace::where('slug', $slug)->first();
        if($workspace)
        {
            $service_name = ProductService::where('type','facilities')->where('workspace_id', $workspace->id)->get()->pluck('name','id');
            if ($lang == '') {
                $lang = !empty($company_settings['defult_language']) ? $company_settings['defult_language'] : 'en';
            }
            \App::setLocale($lang);

            return view('facilities::frontend.index', compact('service_name', 'workspace', 'slug', 'lang'));
        }
        else {
            abort(404);
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
    public function store(Request $request, $slug)
    {
        $services = FacilitiesService::find($request->service_id);

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'service' => 'required',
                'date' => 'required',
                'number' => 'required',
                'email' => 'required',
                'gender' => 'required',
                'person' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'payment_option' => 'required',
            ]
        );
        if ($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('msg', $messages->first());
        }

        if ($request->payment_option == 'Offline') {
            $workspace                          = WorkSpace::where('slug', $slug)->first();
            $facilitiesbooking                  = new FacilitiesBooking();
            $facilitiesbooking->name            = $request->name;
            $facilitiesbooking->service         = isset($services->item_id) ? $services->item_id :'';
            $facilitiesbooking->date            = $request->date;
            $facilitiesbooking->number          = $request->number;
            $facilitiesbooking->email           = $request->email;
            $facilitiesbooking->gender          = $request->gender;
            $facilitiesbooking->person          = $request->person;
            $facilitiesbooking->start_time      = $request->start_time;
            $facilitiesbooking->end_time        = $request->end_time;
            $facilitiesbooking->payment_option  = $request->payment_option;
            $facilitiesbooking->workspace       = $workspace->id;
            $facilitiesbooking->created_by      = $workspace->created_by;
            $facilitiesbooking->save();

            event(new CreateFacilitiesBooking($request,$facilitiesbooking));

            $msg =  __('The booking has been created successfully.');
            return redirect()->back()->with('msg', $msg);
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

    public function searchFacilitiesBooking(Request $request, $id, $slug)
    {
        $returnHTML = '';
        $formattedSelectedDate = $request->date;
        $gender = $request->gender;
        $person = $request->person;
        $facilities_working = FacilitiesWorking::where('workspace', $id)->first();

        if (!empty($facilities_working)) {
            if ($facilities_working->holiday_setting == 'on') {
                $holidays = Holiday::where('workspace', $id)
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

            if (strpos($facilities_working->day_of_week, $day) === false) {
                $response = [
                    'is_success' => false,
                    'message' => __('Selected date is a day off week. Please choose another date.'),
                    'html' => null,
                ];
                return $response;
            }

            $workspace = WorkSpace::where('id', $id)->first();
            $service = FacilitiesService::where('item_id', $request->service)->where('workspace', $id)->first();

            $total_working_time = date('G:i', strtotime($facilities_working->closing_time) - strtotime($facilities_working->opening_time));
            $service_time = $service->time;

            $total_working_minutes = $this->convertToMinutes($total_working_time);
            $service_minutes = $this->convertToMinutes($service_time);

            $slots_in_one_day = floor($total_working_minutes / $service_minutes);

            // Initialize an array to store time slots
            $slot = [];

            // Generate time slots
            $start_time = strtotime($facilities_working->opening_time);
            $service_timess = strtotime($service_time);


            $breck_start = $facilities_working->breck_start;
            $breck_end = $facilities_working->breck_end;

            for ($i = 0; $i < $slots_in_one_day; $i++) {
                $end_time = $start_time + $service_timess;

                // Convert timestamp to 'H:i:s' format
                $formatted_start_time = date('H:i:s', $start_time);
                $formatted_end_time = date('H:i:s', $end_time);

                // Check if the slot is already booked
                $is_slot_booked = FacilitiesBooking::where(function ($query) use ($formatted_start_time, $formatted_end_time , $request) {
                    $query->whereTime('start_time', '>=', $formatted_start_time)
                        ->whereTime('end_time', '<=', $formatted_end_time)
                        ->where('service' , $request->service)
                        ->whereDate('date', $request->date);
                })->count();

                $s = date('G:i', $start_time);
                $e = date('G:i', $end_time);

                // If the slot is booked or within the buffer period, mark it as unavailable
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
            $price = $service->item->sale_price * $request->person;

            $returnHTML = view('facilities::frontend.append', compact('service', 'slot', 'gender', 'id', 'slug', 'person', 'workspace', 'formattedSelectedDate','price'))->render();

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
}
