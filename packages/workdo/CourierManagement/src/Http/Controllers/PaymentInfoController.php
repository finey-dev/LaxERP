<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Entities\CourierBranch;
use Workdo\CourierManagement\Entities\PackageCategory;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Workdo\CourierManagement\Entities\CourierPackageInfo;
use Workdo\CourierManagement\Entities\CourierPackagePayment;
use Workdo\CourierManagement\Entities\Trackingstatus;
use Workdo\CourierManagement\Entities\CourierTracking;
use App\Models\WorkSpace;
use Illuminate\Support\Carbon;
use Workdo\CourierManagement\DataTables\CourierPaymentDatatable;
use Workdo\CourierManagement\Events\Manualpaymentdatadelete;


class PaymentInfoController extends Controller
{
    public function index(CourierPaymentDatatable $dataTable,Request $request)
    {
        if (Auth::user()->isAbleTo('courier payment')) {
            $workspace = Workspace::where('id', getActiveWorkSpace())->first();            
            $trackingStatus = Trackingstatus::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->orderby('order', 'asc')->get();
            return $dataTable->render('courier-management::payment_information.index', compact('workspace', 'trackingStatus'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied !!!'));
        }
    }


    public function create()
    {
        return view('courier-management::create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        return view('courier-management::show');
    }

    public function edit($id)
    {
        return view('courier-management::edit');
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy(Request $request, $trackingId)
    {
        $trackingId = decrypt($trackingId);
        $courierPaymentDetails = CourierPackagePayment::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();


        if ($courierPaymentDetails) {
            event(new Manualpaymentdatadelete($courierPaymentDetails, $request));

            if (file_exists($courierPaymentDetails->payment_receipt)) {
                unlink($courierPaymentDetails->payment_receipt);
            }
            $courierPaymentDetails->delete();

            return redirect()->route('courier.all.payment')->with('success', __('The payment has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Data Not Found !!!'));
        }
    }
}
