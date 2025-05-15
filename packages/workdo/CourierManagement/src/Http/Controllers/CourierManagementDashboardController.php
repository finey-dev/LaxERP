<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Google\Service\Area120Tables\Resource\Workspaces;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth; 
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use App\Models\WorkSpace;

class CourierManagementDashboardController extends Controller
{
    public function __construct()
    {
        if (module_is_active('GoogleAuthentication')) {
            $this->middleware('2fa');
        }
    }
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('couriermanagement dashboard manage')) {
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
                ->select(
                    'courier_receiver_details.id as courierReceiverId',
                    'courier_receiver_details.*',
                    'cpi.id as courierPackageInfoId',
                    'cpi.*',
                    'cpp.*'
                )
                ->get();
            $totalIncome = $courierDetails->sum('price');
            $totalDeliveredCourier = $courierDetails->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('is_courier_delivered', 1)->count();
            $totalCourier = CourierReceiverDetails::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $workspace = WorkSpace::where('id', getActiveWorkSpace())->where('created_by', creatorId())->first();

            $getCurrentMonthData = CourierReceiverDetails::with([
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
                })->whereYear('courier_receiver_details.created_at', now()->year)
                ->whereMonth('courier_receiver_details.created_at', now()->month)
                ->select(
                    'courier_receiver_details.id as courierReceiverId',
                    'courier_receiver_details.*',
                    'cpi.id as courierPackageInfoId',
                    'cpi.*'
                )->orderby('courierReceiverId', 'desc')
                ->limit(5)
                ->get();
            $currentYear = date('Y');

            $data['incExpBarChartData']  = \Workdo\CourierManagement\Entities\CourierReceiverDetails::getincExpBarChartData();

            return view('courier-management::dashboard.dashboard', compact('totalCourier', 'totalIncome', 'courierDetails', 'totalDeliveredCourier', 'workspace', 'getCurrentMonthData', 'currentYear', 'data'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        //
    }


    public function destroy($id)
    {
        //
    }
}
