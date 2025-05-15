<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\DataTables\MovementHistoryDataTable;
use Workdo\RepairManagementSystem\Entities\RepairMovementHistory;

class RepairMovementHistoryController extends Controller
{
    public function index(MovementHistoryDataTable $dataTable, $id)
    {
        if (Auth::user()->isAbleTo('repair movement history show')) {
            $repair_movement_hostories = RepairMovementHistory::with('repairOrderRequest')->where('repair_id',$id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return $dataTable->with('id',$id)->render('repair-management-system::repair-movement.index', compact('repair_movement_hostories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
