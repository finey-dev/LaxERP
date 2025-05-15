<?php
// This file use for handle company setting page

namespace Workdo\SupportTicket\Http\Controllers\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\SupportTicket\Entities\TicketField;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($settings)
    {
        $fields = TicketField::where('workspace_id',getActiveWorkSpace())->where('created_by',\Auth::user()->id)->orderBy('order')->get();
        if($fields->count() < 1)
        {
            TicketField::defultadd();
        }

        return view('support-ticket::company.settings.index',compact('settings','fields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
}
