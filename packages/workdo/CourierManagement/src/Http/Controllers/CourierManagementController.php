<?php

namespace Workdo\CourierManagement\Http\Controllers;


use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Entities\CourierBranch;
use Workdo\CourierManagement\Entities\PackageCategory;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Workdo\CourierManagement\Entities\CourierPackageInfo;
use Workdo\CourierManagement\Entities\CourierPackagePayment;
use Workdo\CourierManagement\Entities\Trackingstatus;
use Workdo\CourierManagement\Entities\CourierTracking;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Crypt;
use App\Models\WorkSpace;
use Google\Service\Area120Tables\Resource\Workspaces;
use Illuminate\Support\Facades\Session;
use Workdo\CourierManagement\Events\Couriercreate;
use Workdo\CourierManagement\Events\Courierupdate;
use Workdo\CourierManagement\Events\Courierdelete;
use Workdo\CourierManagement\Events\Couriertrackingstatuschange;
use Workdo\CourierManagement\Events\Changecouriertrackingstatus;
use Workdo\CourierManagement\Events\CourierRequestApprove;
use Workdo\CourierManagement\Events\CourierRequestReject;
use Workdo\CourierManagement\Events\DestroyPendingCourierRequest;
use Workdo\CourierManagement\DataTables\CourierDataTable;
use Workdo\CourierManagement\DataTables\PendingCourierDatatable;
use App\Models\Setting;




class CourierManagementController extends Controller
{
    public function courierSettingsStore(Request $request)
    {
        if ($request->has('courier_setting_api_key')) {
            $validator = Validator::make($request->all(), [
                'courier_setting_api_key' => 'required|string',
            ]);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
        }
        $getActiveWorkSpace = getActiveWorkSpace();
        $creatorId = creatorId();
        if ($request->has('courier_setting_is_enable')) {
            $post = $request->all();

            foreach ($post as $key => $value) {
                $data = [
                    'key' => $key,
                    'workspace' => $getActiveWorkSpace,
                    'created_by' => $creatorId,
                ];


                Setting::updateOrInsert($data, ['value' => $value]);
            }
        } else {
            $data = [
                'key' => 'courier_setting_is_enable',
                'workspace' => $getActiveWorkSpace,
                'created_by' => $creatorId,
            ];
            Setting::updateOrInsert($data, ['value' => 'off']);
        }
        // Settings Cache forget
        comapnySettingCacheForget();
        return redirect()->back()->with('success', __('Courier API Key Has Been Save Sucessfully.'));
    }
    public function index(CourierDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('courier manage')) {
            $workspace = Workspace::where('id', getActiveWorkSpace())->first();
            return $dataTable->render('courier-management::courier.index', compact('workspace'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied'));
        }
    }

    public function getBranch(Request $request)
    {
        $destBranch = CourierBranch::where('id', '!=', $request->branchId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
        return response()->json($destBranch);
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('courier create')) {
            $serviceType = Servicetype::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $courierBranch = CourierBranch::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $packageCategory = PackageCategory::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'CourierManagement')->where('sub_module', 'Courier')->get();
            } else {
                $customFields = null;
            }
            return view('courier-management::courier.create', compact('serviceType', 'courierBranch', 'packageCategory', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied..'));
        }
    }


    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('courier create')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'sender_name' => 'required',
                    'sender_mobileno' => 'required',
                    'sender_email_address' => 'required',
                    'receiver_name' => 'required',
                    'receiver_mobileno' => 'required',
                    'service_type' => 'required',
                    'source_branch' => 'required',
                    'destination_branch' => 'required',
                    'receiver_address' => 'required',
                    'package_title' => 'required',
                    'package_category' => 'required',
                    'weight' => 'required',
                    'height' => 'required',
                    'width' => 'required',
                    'price' => 'required',
                    'delivery_date' => 'required',
                    'package_description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }

            $getTrackingStatus = Trackingstatus::where('status_name', 'Pending')->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
            if ($getTrackingStatus) {
                $receiverDetails = CourierReceiverDetails::create([
                    'sender_name' => $request->sender_name,
                    'tracking_id' => time(),
                    'sender_mobileno' => $request->sender_mobileno,
                    'sender_email' => $request->sender_email_address,
                    'receiver_name' => $request->receiver_name,
                    'receiver_mobileno' => $request->receiver_mobileno,
                    'delivery_address' => $request->receiver_address,
                    'service_type' => $request->service_type,
                    'source_branch' => $request->source_branch,
                    'destination_branch' => $request->destination_branch,
                    'payment_status' => 'pending',
                    'is_payment_done' => 0,
                    'is_approve' => 1,
                    'workspace_id' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ]);



                $courierPackageInfo = CourierPackageInfo::create([
                    'tracking_id' => $receiverDetails->tracking_id,
                    'package_title' => $request->package_title,
                    'package_description' => $request->package_description,
                    'height' => $request->height,
                    'width' => $request->width,
                    'weight' => $request->weight,
                    'package_category' => $request->package_category,
                    'tracking_status' => $getTrackingStatus->id ?? 'pending',
                    'tracking_status_log' => $getTrackingStatus->id ?? 'pending',
                    'price' => $request->price,
                    'expected_delivery_date' => $request->delivery_date,
                    'workspace_id' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ]);

                // Add Default pending status in courier tracking
                $courierTracking = new  CourierTracking;
                $courierTracking->tracking_id = $receiverDetails->tracking_id;
                $courierTracking->tracking_status_id = $getTrackingStatus->id;
                $courierTracking->date = now();
                $courierTracking->workspace = getActiveWorkSpace();
                $courierTracking->created_by = creatorId();
                $courierTracking->save();

                if (module_is_active('CustomField')) {
                    \Workdo\CustomField\Entities\CustomField::saveData($receiverDetails, $request->customField);
                }
                $company_settings = getCompanyAllSetting();
                if (!empty($company_settings['New Courier']) && $company_settings['New Courier']  == true) {

                    // $userData = CourierReceiverDetails::where('sender_email', $request->sender_email_address)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();

                    $userData = CourierReceiverDetails::where('sender_email', $request->sender_email_address)
                        ->where('workspace_id', getActiveWorkSpace())
                        ->where('created_by', creatorId())
                        ->latest('created_at')
                        ->first();
                    $activeWorkspace = getActiveWorkSpace();
                    $workspace = WorkSpace::where('id', $activeWorkspace)->where('created_by', creatorId())->first();
                    $trackingUrl = route('find.courier', ['workspaceSlug' => $workspace->slug]);
                    $link = '<a href="' . $trackingUrl . '">Click here to track your courier</a>';

                    $uArr = [
                        'tracking_id' => $userData->tracking_id,
                        'tracking_url' => $link,
                    ];
                    try {
                        $resp = EmailTemplate::sendEmailTemplate('New Courier', [$userData->sender_email], $uArr);
                    } catch (\Exception $e) {
                        $resp['error'] = $e->getMessage();
                    }
                }

                event(new Couriercreate($receiverDetails, $courierPackageInfo, $request));

                return redirect()->route('courier')->with('success', __('The courier has been created successfully'));
            } else {
                return redirect()->route('courier')->with('error', __('Tracking Status Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied..'));
        }
    }



    public function edit(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier edit')) {
            $trackingId = decrypt($trackingId);
            $courierDetails = CourierReceiverDetails::join('courier_package_infos', 'courier_package_infos.tracking_id', '=', 'courier_receiver_details.tracking_id')
                ->select('courier_receiver_details.*', 'courier_package_infos.*')
                ->where('courier_receiver_details.tracking_id', $trackingId)->first();


            $serviceType = Servicetype::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $courierBranch = CourierBranch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $packageCategory = PackageCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $trackingStatusLog = explode(',', $courierDetails->tracking_status_log);
            $trackingStatus = Trackingstatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->whereNotIn('id', $trackingStatusLog)->orderby('order', 'asc')->get();

            if (module_is_active('CustomField')) {
                $courierDetails->customField = \Workdo\CustomField\Entities\CustomField::getData($courierDetails, 'CourierManagement', 'Courier');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'CourierManagement')->where('sub_module', 'Courier')->get();
            } else {
                $customFields = null;
            }

            if ($courierDetails !== 'null') {
                return view('courier-management::courier.edit', compact('serviceType', 'courierBranch', 'packageCategory', 'courierDetails', 'trackingStatus', 'customFields'));
            } else {
                return redirect()->back()->with('error', __('Something Went Wrong !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied..'));
        }
    }


    public function update(Request $request, $trackingId)
    {


        if (Auth::user()->isAbleTo('courier edit')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'sender_name' => 'required',
                    'sender_mobileno' => 'required',
                    'sender_email_address' => 'required',
                    'receiver_name' => 'required',
                    'receiver_mobileno' => 'required',
                    'service_type' => 'required',
                    'source_branch' => 'required',
                    'destination_branch' => 'required',
                    'receiver_address' => 'required',
                    'package_title' => 'required',
                    'package_category' => 'required',
                    'weight' => 'required',
                    'height' => 'required',
                    'width' => 'required',
                    'price' => 'required',
                    'delivery_date' => 'required',
                    'package_description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $message = $validator->getMessageBag();
                return redirect()->back()->with('error', $message->first());
            }

            $trackingId = decrypt($trackingId);
            $receiverDetails = CourierReceiverDetails::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();


            $receiverDetails->sender_name = $request->sender_name;
            $receiverDetails->sender_mobileno = $request->sender_mobileno;
            $receiverDetails->sender_email = $request->sender_email_address;
            $receiverDetails->receiver_name = $request->receiver_name;
            $receiverDetails->receiver_mobileno = $request->receiver_mobileno;
            $receiverDetails->delivery_address = $request->receiver_address;
            $receiverDetails->service_type = $request->service_type;
            $receiverDetails->source_branch = $request->source_branch;
            $receiverDetails->destination_branch = $request->destination_branch;
            $receiverDetails->save();
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($receiverDetails, $request->customField);
            }

            $courierPackageInfo = CourierPackageInfo::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
            $courierPackageInfo->package_title = $request->package_title;
            $courierPackageInfo->package_description = $request->package_description;
            $courierPackageInfo->height = $request->height;
            $courierPackageInfo->width = $request->width;
            $courierPackageInfo->weight = $request->weight;
            $courierPackageInfo->package_category = $request->package_category;
            $courierPackageInfo->price = $request->price;
            $courierPackageInfo->expected_delivery_date = $request->delivery_date;
            if ($request->tracking_status !== null) {
                $courierPackageInfo->tracking_status = $request->tracking_status;
                if ($courierPackageInfo->tracking_status_log == 'pending') {
                    $courierPackageInfo->tracking_status_log = $request->tracking_status;
                } else {
                    $courierPackageInfo->tracking_status_log = $courierPackageInfo->tracking_status_log . ',' . $request->tracking_status;
                }
            }

            $courierPackageInfo->save();

            // check courir is delivered or not
            $trackingStatusLog = explode(',', $receiverDetails->packageInformarmation->tracking_status_log);
            $trackingStatus = Trackingstatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->whereNotIn('id', $trackingStatusLog)->orderby('order', 'asc')->get();
            if (count($trackingStatus) <= 0) {
                $updateData = CourierReceiverDetails::where('tracking_id', $trackingId)->first();
                $updateData->is_courier_delivered = 1;
                $updateData->save();
            }
            // end

            if ($request->tracking_status != null) {
                $courierTracking = new  CourierTracking;
                $courierTracking->tracking_id = $trackingId;
                $courierTracking->tracking_status_id = $request->tracking_status;
                $courierTracking->date = now();
                $courierTracking->workspace = getActiveWorkSpace();
                $courierTracking->created_by = creatorId();
                $courierTracking->save();
                event(new Couriertrackingstatuschange($courierTracking, $request));
            }

            event(new Courierupdate($receiverDetails, $courierPackageInfo, $request));



            return redirect()->route('courier')->with('success', __('The courier details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied..'));
        }
    }


    public function destroy(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier delete')) {
            $trackingId = decrypt($trackingId);
            $receiverData = CourierReceiverDetails::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
            $packageData = CourierPackageInfo::where('tracking_id', $trackingId)->first();
            $packagePaymentData = CourierPackagePayment::where('tracking_id', $trackingId)->first();
            $courierTrackingStatus = CourierTracking::where('tracking_id', $trackingId)->get();


            event(new Courierdelete($receiverData, $packageData, $packagePaymentData, $courierTrackingStatus));

            if (module_is_active('CustomField')) {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'CourierManagement')->where('sub_module', 'Courier')->get();
                foreach ($customFields as $customField) {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $receiverData->id)->where('field_id', $customField->id)->first();
                    if (!empty($value)) {
                        $value->delete();
                    }
                }
            }
            $receiverData->delete();
            $packageData->delete();
            if ($packagePaymentData !== null) {
                $packagePaymentData->delete();
            }
            if (count($courierTrackingStatus) > 0) {
                foreach ($courierTrackingStatus as $status) {
                    $status->delete();
                }
            }
            return redirect()->route('courier')->with('success', __('The courier has been deleted'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied..');
        }
    }

    public function findCourier(Request $request, $slug)
    {
        $workspace = WorkSpace::where('slug', $slug)->first();
        if ($workspace) {
            return view('courier-management::track_courier.find_courier', compact('workspace'));
        } else {
            abort(404);
        }
    }

    public function trackCourier(Request $request, $slug)
    {
        $workspace = Workspace::where('slug', $slug)->first();
        $trackingId = CourierReceiverDetails::where('tracking_id', $request->tracking_number)->where('is_approve', 1)->first();
        if (isset($trackingId) && $trackingId != NULL) {
            $checkEmail = CourierReceiverDetails::where('sender_email', $request->email)->where('tracking_id', $trackingId->tracking_id)->first();
            if (isset($checkEmail) && $checkEmail !== NULL) {
                // Session::put('tracking_status', true);
                $courierDetails = CourierReceiverDetails::with(['packageInformarmation.courier_category', 'packageInformarmation.getTrackingStatus', 'getServiceType', 'getSourceBranch', 'getDestinationBranch'])
                    ->join('courier_package_infos', 'courier_package_infos.tracking_id', '=', 'courier_receiver_details.tracking_id')
                    ->select('courier_receiver_details.*', 'courier_package_infos.*')
                    ->where('courier_receiver_details.tracking_id', $trackingId->tracking_id)->first();
                $workspace = Workspace::where('slug', $slug)->first();
                $allTrackingStatus = Trackingstatus::where('workspace', $workspace->id)->orderby('order', 'asc')->get();
                $currentTrackingStatus = CourierTracking::where('tracking_id', $trackingId->tracking_id)->get();
                if ($courierDetails !== 'null') {
                    return view('courier-management::track_courier.track_courier', compact('courierDetails', 'allTrackingStatus', 'currentTrackingStatus'));
                } else {
                    return redirect()->back()->with('error', __('Something Went Wrong !!!'));
                }
            } else {
                return redirect()->back()->with('error-alert', __('Please Enter Valid Email Address...'));
            }
        } else {
            return redirect()->back()->with('error-alert', __('Your Request Is Not Accepted So , You Can Not Track Your Courier Now...'));
        }
    }


    public function show(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier manage')) {
            $trackingId = decrypt($trackingId);
            $courierDetails = CourierReceiverDetails::with(['packageInformarmation.courier_category', 'packageInformarmation.getTrackingStatus', 'getServiceType', 'getSourceBranch', 'getDestinationBranch'])
                ->join('courier_package_infos', 'courier_package_infos.tracking_id', '=', 'courier_receiver_details.tracking_id')
                ->select('courier_receiver_details.*', 'courier_package_infos.*')
                ->where('courier_receiver_details.tracking_id', $trackingId)->first();
            $allTrackingStatus = Trackingstatus::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->orderby('order', 'asc')->get();
            $currentTrackingStatus = CourierTracking::where('tracking_id', $trackingId)->get();
            $trackingStatusLog = explode(',', $courierDetails->tracking_status_log);
            $trackingStatus = Trackingstatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->whereNotIn('id', $trackingStatusLog)->orderby('order', 'asc')->get();
            if (count($trackingStatus) <= 0) {
                $updateData = CourierReceiverDetails::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('tracking_id', $trackingId)->first();
                $updateData->is_courier_delivered = 1;
                $updateData->save();
            }

            if (module_is_active('CustomField')) {
                $courierDetails->customField = \Workdo\CustomField\Entities\CustomField::getData($courierDetails, 'CourierManagement', 'Courier');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'CourierManagement')->where('sub_module', 'Courier')->get();
            } else {
                $customFields = null;
            }

            return view('courier-management::courier.show', compact('courierDetails', 'allTrackingStatus', 'currentTrackingStatus', 'trackingStatus', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied !!!'));
        }
    }

    public function updateTrackingStatus(Request $request, $trackingId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'tracking_status' => 'required'
            ]
        );
        if ($validator->fails()) {
            $message = $validator->getMessageBag();
            return redirect()->back()->with('error', $message->first());
        }

        $trackingId = decrypt($trackingId);
        $courierInfo = CourierPackageInfo::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();
        $courierInfo->tracking_status = $request->tracking_status;
        if ($courierInfo->tracking_status_log == 'pending') {
            $courierInfo->tracking_status_log = $request->tracking_status;
        } else {
            $courierInfo->tracking_status_log = $courierInfo->tracking_status_log . ',' . $request->tracking_status;
        }
        $courierInfo->save();

        $courierTracking = new  CourierTracking;
        $courierTracking->tracking_id = $trackingId;
        $courierTracking->tracking_status_id = $request->tracking_status;
        $courierTracking->date = now();
        $courierTracking->workspace = getActiveWorkSpace();
        $courierTracking->created_by = creatorId();
        $courierTracking->save();

        // check courir is delivered or not
        $trackingStatusLog = explode(',', $courierInfo->tracking_status_log);
        $trackingStatus = Trackingstatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->whereNotIn('id', $trackingStatusLog)->orderby('order', 'asc')->get();
        if (count($trackingStatus) <= 0) {
            $updateData = CourierReceiverDetails::where('tracking_id', $trackingId)->first();
            $updateData->is_courier_delivered = 1;
            $updateData->save();
        }
        // end


        event(new Changecouriertrackingstatus($courierInfo, $courierTracking, $request));
        return redirect()->back()->with('success', 'Status Updated Successfully...');
    }

    public function createPublicCourierRequest(Request $request, $workspaceSlug)
    {
        $workspace = Workspace::where('slug', $workspaceSlug)->first();
        if ($workspace) {
            $serviceType = Servicetype::where('workspace', $workspace->id)->get();
            $courierBranch = CourierBranch::where('workspace', $workspace->id)->get();
            $packageCategory = PackageCategory::where('workspace', $workspace->id)->get();
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', $workspace->id)->where('module', '=', 'CourierManagement')->where('sub_module', 'Courier')->get();
            } else {
                $customFields = null;
            }
            return view('courier-management::courier.public_courier_form', compact('workspace', 'serviceType', 'courierBranch', 'packageCategory', 'customFields'));
        } else {
            abort(404);
        }
    }

    public function getDestinationBranch(Request $request, $workspaceId)
    {
        $destBranch = CourierBranch::where('id', '!=', $request->branchId)->where('workspace', $workspaceId)->get();
        return response()->json($destBranch);
    }

    public function storePublicCourierRequestData(Request $request)
    {
        $request->validate([
            'sender_name' => 'required',
            'sender_mobileno' => 'required',
            'sender_email_address' => 'required',
            'receiver_name' => 'required',
            'receiver_mobileno' => 'required',
            'service_type' => 'required',
            'source_branch' => 'required',
            'destination_branch' => 'required',
            'receiver_address' => 'required',
            'package_title' => 'required',
            'package_category' => 'required',
            'weight' => 'required',
            'height' => 'required',
            'width' => 'required',
            'price' => 'required',
            'delivery_date' => 'required',
            'package_description' => 'required',
        ]);

        $workspace = Workspace::where('id', $request->workspace_id)->first();
        $getTrackingStatus = Trackingstatus::where('status_name', 'Pending')->where('workspace', $workspace->id)->where('created_by', $workspace->created_by)->first();
        $receiverDetails = CourierReceiverDetails::create([
            'sender_name' => $request->sender_name,
            'tracking_id' => time(),
            'sender_mobileno' => $request->sender_mobileno,
            'sender_email' => $request->sender_email_address,
            'receiver_name' => $request->receiver_name,
            'receiver_mobileno' => $request->receiver_mobileno,
            'delivery_address' => $request->receiver_address,
            'service_type' => $request->service_type,
            'source_branch' => $request->source_branch,
            'destination_branch' => $request->destination_branch,
            'payment_status' => 'pending',
            'is_payment_done' => 0,
            'is_approve' => null,
            'workspace_id' => $workspace->id,
            'created_by' => $workspace->created_by,
        ]);

        $courierPackageInfo = CourierPackageInfo::create([
            'tracking_id' => $receiverDetails->tracking_id,
            'package_title' => $request->package_title,
            'package_description' => $request->package_description,
            'height' => $request->height,
            'width' => $request->width,
            'weight' => $request->weight,
            'package_category' => $request->package_category,
            'tracking_status' => $getTrackingStatus->id,
            'tracking_status_log' => $getTrackingStatus->id,
            'price' => $request->price,
            'expected_delivery_date' => $request->delivery_date,
            'workspace_id' => $workspace->id,
            'created_by' => $workspace->created_by,
        ]);


        // Add Default pending status in courier tracking
        $courierTracking = new  CourierTracking;
        $courierTracking->tracking_id = $receiverDetails->tracking_id;
        $courierTracking->tracking_status_id = $getTrackingStatus->id;
        $courierTracking->date = now();
        $courierTracking->workspace = $workspace->id;
        $courierTracking->created_by = $workspace->created_by;
        $courierTracking->save();

        if (module_is_active('CustomField')) {
            \Workdo\CustomField\Entities\CustomField::saveData($receiverDetails, $request->customField);
        }

        $company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
        if (!empty($company_settings['New Courier']) && $company_settings['New Courier']  == true) {

            // $userData = CourierReceiverDetails::where('sender_email', $request->sender_email_address)->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->first();

            $userData = CourierReceiverDetails::where('sender_email', $request->sender_email_address)
                ->where('workspace_id', $workspace->id)
                ->latest('created_at')
                ->first();
            $activeWorkspace = $workspace->id;
            $workspace = WorkSpace::where('id', $activeWorkspace)->first();
            $trackingUrl = route('find.courier', ['workspaceSlug' => $workspace->slug]);
            $link = '<a href="' . $trackingUrl . '">Click here to track your courier</a>';

            $uArr = [
                'tracking_id' => $userData->tracking_id,
                'tracking_url' => $link,
            ];
            try {
                $resp = EmailTemplate::sendEmailTemplate('New Courier', [$userData->sender_email], $uArr, $workspace->created_by, $workspace->id);
            } catch (\Exception $e) {
                $resp['error'] = $e->getMessage();
            }
        }

        event(new Couriercreate($receiverDetails, $courierPackageInfo, $request));



        return redirect()->back()->with('success', __('The courier has been created successfully'));
    }

    public function getPendingCourierRequest(PendingCourierDatatable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('courier pending request manage')) {
            $workspace = Workspace::where('id', getActiveWorkSpace())->first();
            return $dataTable->render('courier-management::pending_courier.index', compact('workspace'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied'));
        }
    }

    public function approveCourierRequest(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier pending request approve')) {
            $trackingId = CourierReceiverDetails::where('tracking_id', $trackingId)->first();
            if ($trackingId) {
                $trackingId->is_approve = 1;
                $trackingId->save();

                $company_settings = getCompanyAllSetting();
                if (!empty($company_settings['Courier Request Accept']) && $company_settings['Courier Request Accept']  == true) {
                    $activeWorkspace = getActiveWorkSpace();
                    $workspace = WorkSpace::where('id', $activeWorkspace)->where('created_by', creatorId())->first();
                    $trackingUrl = route('find.courier', ['workspaceSlug' => $workspace->slug]);
                    $link = '<a href="' . $trackingUrl . '">Click here to track your courier</a>';

                    $uArr = [
                        'tracking_id' => $trackingId->tracking_id,
                        'tracking_url' => $link,
                    ];

                    try {
                        $resp = EmailTemplate::sendEmailTemplate('Courier Request Accept', [$trackingId->sender_email], $uArr);
                    } catch (\Exception $e) {
                        $resp['error'] = $e->getMessage();
                    }
                }

                event(new CourierRequestApprove($trackingId));

                return redirect()->back()->with('success', __('The courier request has been accepted'));
            } else {
                return redirect()->back()->with('error', __('Tracking Id Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', 'Permission Denied !!!');
        }
    }

    public function rejectCourierRequest(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier pending request reject')) {
            $trackingId = CourierReceiverDetails::with('getCourierPackageInformation')->where('tracking_id', $trackingId)->first();
            $packageTitle = isset($trackingId->getCourierPackageInformation->package_title) ? $trackingId->getCourierPackageInformation->package_title : '';
            if ($trackingId) {
                $trackingId->is_approve = 0;
                $trackingId->save();

                $company_settings = getCompanyAllSetting();
                if (!empty($company_settings['Courier Request Reject']) && $company_settings['Courier Request Reject']  == true) {
                    $activeWorkspace = getActiveWorkSpace();
                    $workspace = WorkSpace::where('id', $activeWorkspace)->where('created_by', creatorId())->first();
                    $trackingUrl = route('find.courier', ['workspaceSlug' => $workspace->slug]);
                    $link = '<a href="' . $trackingUrl . '">Click here to track your courier</a>';

                    $uArr = [
                        'package_title' => $packageTitle,
                    ];

                    try {
                        $resp = EmailTemplate::sendEmailTemplate('Courier Request Reject', [$trackingId->sender_email], $uArr);
                    } catch (\Exception $e) {
                        $resp['error'] = $e->getMessage();
                    }
                }

                event(new CourierRequestReject($trackingId));
                return redirect()->back()->with('success', __('The courier request has been rejected'));
            } else {
                return redirect()->back()->with('error', __('Tracking Id Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied !!!'));
        }
    }

    public function deletePendingCourierRequest(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier pending request delete')) {
            $trackingId = decrypt($trackingId);
            $receiverData = CourierReceiverDetails::where('tracking_id', $trackingId)->where('workspace_id', getActiveWorkSpace())->first();
            $packageData = CourierPackageInfo::where('tracking_id', $trackingId)->first();
            $packagePaymentData = CourierPackagePayment::where('tracking_id', $trackingId)->first();
            $courierTrackingStatus = CourierTracking::where('tracking_id', $trackingId)->get();


            event(new DestroyPendingCourierRequest($receiverData, $packageData, $packagePaymentData, $courierTrackingStatus));

            if (module_is_active('CustomField')) {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'CourierManagement')->where('sub_module', 'Courier')->get();
                foreach ($customFields as $customField) {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $receiverData->id)->where('field_id', $customField->id)->first();
                    if (!empty($value)) {
                        $value->delete();
                    }
                }
            }
            $receiverData->delete();
            $packageData->delete();
            if ($packagePaymentData !== null) {
                $packagePaymentData->delete();
            }
            if (count($courierTrackingStatus) > 0) {
                foreach ($courierTrackingStatus as $status) {
                    $status->delete();
                }
            }
            return redirect()->route('get.pending.courier.request')->with('success', 'The courier has been deleted');
        } else {
            return redirect()->back()->with('error', 'Permission Denied..');
        }
    }
    public function showCourierPendingRequest(Request $request, $trackingId)
    {
        if (Auth::user()->isAbleTo('courier pending request manage')) {
            $trackingId = decrypt($trackingId);
            $courierDetails = CourierReceiverDetails::with(['packageInformarmation.courier_category', 'packageInformarmation.getTrackingStatus', 'getServiceType', 'getSourceBranch', 'getDestinationBranch'])
                ->join('courier_package_infos', 'courier_package_infos.tracking_id', '=', 'courier_receiver_details.tracking_id')
                ->select('courier_receiver_details.*', 'courier_package_infos.*')
                ->where('courier_receiver_details.tracking_id', $trackingId)->first();

            if (module_is_active('CustomField')) {
                $courierDetails->customField = \Workdo\CustomField\Entities\CustomField::getData($courierDetails, 'CourierManagement', 'Courier');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'CourierManagement')->where('sub_module', 'Courier')->get();
            } else {
                $customFields = null;
            }

            return view('courier-management::pending_courier.show', compact('courierDetails','customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied !!!'));
        }
    }
}
