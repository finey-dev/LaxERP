<?php

namespace Workdo\CourierManagement\Entities;

use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trackingstatus extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\TrackingstatusFactory::new();
    }

    public static function defaultdata($company_id = null, $workspace_id = null)
    {
        $tracking_status = [
            "ti ti-letter-p" => "Pending",
            "ti ti-checks" => "Approve",
            "ti ti-package" =>   "Ready For Packing",
            "ti ti-truck-delivery" => "Ready For Delivery",
            "ti ti-thumb-up" => "On The Way",
            "ti ti-arrow-wave-right-up" =>  "Delivered",
        ];
        $trackingStatusColor = [
            "ffa833",
            "32CD32",
            "4169E1",
            "0000CD",
            "FF7D32",
            "00cc00",
        ];

        if ($company_id == Null) {
            $companys = User::where('type', 'company')->get();
            foreach ($companys as $company) {
                $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
                foreach ($WorkSpaces as $WorkSpace) {
                    foreach ($tracking_status as $iconName => $tracking_stage) {
                        $trackingstatus = Trackingstatus::where('status_name', $tracking_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                        if ($trackingstatus == null) {
                            $trackingstatus = new Trackingstatus();
                            $trackingstatus->icon_name = $iconName;
                            $trackingstatus->status_name = $tracking_stage;
                            $trackingstatus->status_color = array_shift($trackingStatusColor);
                            $trackingstatus->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                            $trackingstatus->created_by = !empty($company->id) ? $company->id : 2;
                            $trackingstatus->save();
                        }
                    }
                }
            }
        } elseif ($workspace_id == Null) {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
            foreach ($WorkSpaces as $WorkSpace) {
                foreach ($tracking_status as $iconName => $tracking_stage) {

                    $trackingstatus = Trackingstatus::where('status_name', $tracking_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                    if ($trackingstatus == null) {
                        $trackingstatus = new Trackingstatus();
                        $trackingstatus->icon_name = $iconName;
                        $trackingstatus->status_name = $tracking_stage;
                        $trackingstatus->status_color = array_shift($trackingStatusColor);
                        $trackingstatus->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                        $trackingstatus->created_by = !empty($company->id) ? $company->id : 2;
                        $trackingstatus->save();
                    }
                }
            }
        } else {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpace = WorkSpace::where('created_by', $company->id)->where('id', $workspace_id)->first();
            foreach ($tracking_status as $iconName => $tracking_stage) {

                $trackingstatus = Trackingstatus::where('status_name', $tracking_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                if ($trackingstatus == null) {
                    $trackingstatus = new Trackingstatus();
                    $trackingstatus->icon_name = $iconName;
                    $trackingstatus->status_name = $tracking_stage;
                    $trackingstatus->status_color = array_shift($trackingStatusColor);
                    $trackingstatus->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                    $trackingstatus->created_by = !empty($company->id) ? $company->id : 2;
                    $trackingstatus->save();
                }
            }
        }
    }
}
