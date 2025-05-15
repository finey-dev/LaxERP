<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Facilities\Entities\FacilitiesBooking;
use Workdo\Facilities\Entities\FacilitiesBookingOrder;
use Workdo\Facilities\Entities\FacilitiesReceipt;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Facilities\Events\UpdateStatusFacilitiesBooking;
use Workdo\Facilities\DataTables\ReceiptDataTable;

class FacilitiesBookingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('facilities booking order manage')) {
            $stages = FacilitiesBookingOrder::$orderstage;
            $bookings = FacilitiesBooking::select('facilities_bookings.*', 'users.name as user_name')->leftjoin('users', 'users.id', 'facilities_bookings.client_id')->where('facilities_bookings.created_by', creatorId())->where('facilities_bookings.workspace', getActiveWorkSpace());

            if (Auth::user()->type == 'company') {
                $bookings = $bookings->get()->groupBy('stage_id')->toArray();
            } else {
                $bookings = $bookings->where('client_id', Auth::user()->id)->get()->groupBy('stage_id')->toArray();
            }

            return view('facilities::orders.index', compact('stages', 'bookings'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');

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

    public function stageOrder(Request $request)
    {
        if (Auth::user()->isAbleTo('facilitiesbookingorder move')) {
            $facilitiesBooking = FacilitiesBooking::find($request->booking_id);

            if (!$facilitiesBooking) {
                return response()->json(['error' => __('Booking Not Found.')], 404);
            }

            $facilitiesreceipt = FacilitiesReceipt::firstOrNew(['booking_id' => $request->booking_id]);
            if ($request->stage_id == 2) {

                $facilitiesreceipt->name = $facilitiesBooking->name;
                $facilitiesreceipt->client_id = $facilitiesBooking->client_id;
                $facilitiesreceipt->service = $facilitiesBooking->service;
                $facilitiesreceipt->number = $facilitiesBooking->number;
                $facilitiesreceipt->gender = $facilitiesBooking->gender;
                $facilitiesreceipt->start_time = $facilitiesBooking->start_time;
                $facilitiesreceipt->end_time = $facilitiesBooking->end_time;

                $service = ProductService::find($facilitiesBooking->service);

                if (!empty($service->tax_id)) {
                    $totalTaxRate = 0;
                    $totalTaxPrice = 0;
                    $totalPrice = 0;
                    $taxes = \App\Models\Invoice::tax($service->tax_id);
                    foreach ($taxes as $tax) {
                        $taxPrice = \App\Models\Invoice::taxRate(
                            $tax->rate,
                            $service->sale_price,
                            $facilitiesBooking->person,
                            $service->discount,
                        );
                        $totalTaxPrice += $taxPrice;
                        $totalPrice = $totalTaxPrice + ($facilitiesBooking->person * $service->sale_price);
                    }
                } else {
                    $totalPrice = $facilitiesBooking->person * $service->sale_price;
                }
                $facilitiesreceipt->price = isset($totalPrice) ? $totalPrice : 0;
                $facilitiesreceipt->workspace = getActiveWorkSpace();
                $facilitiesreceipt->created_by = creatorId();

                $facilitiesreceipt->save();
            }

            $facilitiesBooking['stage_id'] = $request->stage_id;
            $facilitiesBooking->save();

            event(new UpdateStatusFacilitiesBooking($request , $facilitiesBooking));
            
            return response()->json(['success' => __('Booking Order Move Successfully.')]);

        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function receipt(ReceiptDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('facilities booking receipt manage')) {

            return $dataTable->render('facilities::booking.receipt');
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function receiptShow(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('facilities booking receipt show')) {
            $facilitiesreceipt = FacilitiesReceipt::find($id);

            return view('facilities::booking.receiptshow', compact('facilitiesreceipt'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
