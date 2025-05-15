<?php
// This file use for handle company setting page

namespace Workdo\Recruitment\Http\Controllers\Company;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Workdo\Hrm\Entities\ExperienceCertificate;
use Workdo\Hrm\Entities\IpRestrict;
use Workdo\Hrm\Entities\JoiningLetter;
use Workdo\Hrm\Entities\NOC;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($settings)
    {
        if (Auth::check() && module_is_active('Recruitment')) {
            return view('recruitment::company.settings.index', compact('settings'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
}
