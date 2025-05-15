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
use App\Models\EmailTemplate;
use Workdo\CourierManagement\Events\Manualpaymentdatastore;
use Workdo\CourierManagement\Events\Manualpaymentdataupdate;




class ManualPaymentController extends Controller
{
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


    public function edit(Request $request, $trackingId)
    {
        $trackingId = decrypt($trackingId);

        $courierDetails = CourierReceiverDetails::with([
            'packageInformarmation.courier_category',
            'packageInformarmation.getTrackingStatus',
            'getServiceType',
            'getSourceBranch',
            'getDestinationBranch'
        ])
            ->join('courier_package_infos as cpi', function ($join) {
                $join->on('courier_receiver_details.tracking_id', '=', 'cpi.tracking_id')
                    ->where('courier_receiver_details.workspace_id', '=', getActiveWorkSpace())
                    ->where('courier_receiver_details.created_by', '=', creatorId());
            })
            ->join('courier_package_payments as cpp', function ($join) {
                $join->on('courier_receiver_details.tracking_id', '=', 'cpp.tracking_id')
                    ->on('cpi.id', '=', 'cpp.courier_package_id')
                    ->where('courier_receiver_details.workspace_id', '=', getActiveWorkSpace())
                    ->where('courier_receiver_details.created_by', '=', creatorId());
            })
            ->where('courier_receiver_details.tracking_id', $trackingId)
            ->select(
                'courier_receiver_details.id as courierReceiverId',
                'courier_receiver_details.*',
                'cpi.id as courierPackageInfoId',
                'cpi.*',
                'cpp.*'
            )
            ->first();
        return view('courier-management::payment.edit', compact('courierDetails'));
    }


    public function update(Request $request, $trackingId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_date' => 'required',
                'description' => 'required',
                'pay_amount' => 'required',
            ]
        );
        if ($validator->fails()) {
            $message = $validator->getMessageBag();
            return redirect()->back()->with('error', $message->first());
        }
        $trackingId = decrypt($trackingId);
        $paymentDetails = CourierPackagePayment::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
        if ($paymentDetails) {

            if ($request->hasFile('payment_receipt')) {

                $filenameWithExt = time() . '_' . $request->file('payment_receipt')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('payment_receipt')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                if (file_exists($paymentDetails->payment_receipt)) {
                    unlink($paymentDetails->payment_receipt);
                }

                $path = upload_file($request, 'payment_receipt', $fileNameToStore, 'courier_management/payment_receipt');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                    $paymentDetails->payment_receipt =  $url;
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            $paymentDetails->payment_date = $request->payment_date;
            $paymentDetails->price = $request->pay_amount;
            $paymentDetails->description = $request->description;
            $paymentDetails->save();

            $courierInfo = CourierPackageInfo::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
            if ($courierInfo) {
                $courierInfo->price = $request->pay_amount;
                $courierInfo->save();
            } else {
                return redirect()->back()->with('error', _('Payment Details Not Found!!!'));
            }
            event(new Manualpaymentdataupdate($paymentDetails, $courierInfo, $request));
            return redirect()->back()->with('success', __('The payment details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Payment Details Not Found!!!'));
        }
    }

    public function courierPayment(Request $request, $trackingId)
    {
        $trackingId = decrypt($trackingId);
        $courierDetails = CourierReceiverDetails::with(['packageInformarmation.courier_category', 'packageInformarmation.getTrackingStatus', 'getServiceType', 'getSourceBranch', 'getDestinationBranch'])
            ->join('courier_package_infos as cpi', 'cpi.tracking_id', '=', 'courier_receiver_details.tracking_id')
            ->select(
                'courier_receiver_details.id as courier_receiver_id',
                'courier_receiver_details.*',
                'cpi.id as courier_package_id',
                'cpi.*'
            )
            ->where('courier_receiver_details.tracking_id', $trackingId)->first();
        return view('courier-management::payment.create', compact('courierDetails'));
    }

    public function makePayment(Request $request, $trackingId, $courierPackageId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_date' => 'required',
                'description' => 'required',
                'payment_receipt' => 'required',
            ]
        );
        if ($validator->fails()) {
            $message = $validator->getMessageBag();
            return redirect()->back()->with('error', $message->first());
        }


        if (Auth::user()->isAbleTo('courier edit')) {
            $trackingId = decrypt($trackingId);
            $courierPackageId = decrypt($courierPackageId);

            if ($request->hasFile('payment_receipt')) {

                $filenameWithExt = time() . '_' . $request->file('payment_receipt')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('payment_receipt')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request, 'payment_receipt', $fileNameToStore, 'courier_management/payment_receipt');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            $courierPayment = new CourierPackagePayment;
            $courierPayment->tracking_id = $trackingId;
            $courierPayment->courier_package_id = $courierPackageId;
            $courierPayment->payment_type = 'cash';
            $courierPayment->payment_status = 'success';
            $courierPayment->payment_date = $request->payment_date;
            $courierPayment->price = $request->pay_amount;
            $courierPayment->payment_receipt =  $url;
            $courierPayment->description = $request->description;
            $courierPayment->workspace_id = getActiveWorkSpace();
            $courierPayment->created_by = creatorId();
            $courierPayment->save();

            $receiverDetails = CourierReceiverDetails::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
            $receiverDetails->payment_type = 'cash';
            $receiverDetails->payment_status = 'success';
            $receiverDetails->is_payment_done = 1;
            $receiverDetails->save();
            event(new Manualpaymentdatastore($courierPayment, $receiverDetails, $request));


            return redirect()->route('courier')->with('success', __('The payment has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied..'));
        }
    }
}
