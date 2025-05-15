<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Workdo\CourierManagement\Entities\CourierPackageInfo;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Entities\CourierBranch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;





use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Workdo\CourierManagement\Http\Controllers\BranchController;

class CourierReceiverDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'sender_name',
        'sender_mobileno',
        'sender_email',
        'receiver_name',
        'receiver_mobileno',
        'delivery_address',
        'service_type',
        'source_branch',
        'destination_branch',
        'payment_type',
        'payment_status',
        'is_payment_done',
        'is_courier_delivered',
        'workspace_id',
        'created_by',
        'is_approve',
    ];

    public function createdBy()
    {
        return  $this->hasOne(User::class, 'id', 'created_by');
    }

    public function packageInformarmation()
    {
        return $this->hasOne(CourierPackageInfo::class, 'tracking_id', 'tracking_id');
    }

    public function getServiceType()
    {
        return $this->hasOne(Servicetype::class, 'id', 'service_type');
    }
    public function getSourceBranch()
    {
        return $this->hasOne(CourierBranch::class, 'id', 'source_branch');
    }
    public function getDestinationBranch()
    {
        return $this->hasOne(CourierBranch::class, 'id', 'destination_branch');
    }

    public function getCourierPackageInformation()
    {
        return $this->hasOne(CourierPackageInfo::class, 'tracking_id', 'tracking_id');
    }

    public function getCourierPaymentInfo()
    {
        return $this->hasOne(CourierPackagePayment::class, 'tracking_id', 'tracking_id');
    }



    public static function getIncExpBarChartData()
    {
        $monthNames = [
            __('January'),
            __('February'),
            __('March'),
            __('April'),
            __('May'),
            __('June'),
            __('July'),
            __('August'),
            __('September'),
            __('October'),
            __('November'),
            __('December'),
        ];

        $dataArr['month'] = $monthNames;

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
            ->selectRaw('MONTH(cpp.payment_date) as incomeMonth')
            ->selectRaw('SUM(cpp.price) as totalIncome') // Replace 'income_column' with the actual column name
            ->groupBy('incomeMonth')
            ->selectRaw('COUNT(courier_receiver_details.id) as totalCourierPerMonth')
            ->get();

        $incomeArr = [];
        $totalCourierArr = [];

        foreach ($monthNames as $monthName) {
            $incomeArr[] = 0; // Initialize each month's income to 0
            $totalCourierArr[] = 0; // Initialize each month's total courier count to 0
        }

        foreach ($courierDetails as $detail) {
            $incomeArr[$detail->incomeMonth - 1] = $detail->totalIncome;
            $totalCourierArr[$detail->incomeMonth - 1] = $detail->totalCourierPerMonth;
        }

        $dataArr['income'] = $incomeArr;
        $dataArr['totalCourier'] = $totalCourierArr;

        return $dataArr;
    }



    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\CourierReceiverDetailsFactory::new();
    }
}
