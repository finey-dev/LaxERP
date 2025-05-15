<?php
// This file use for handle company setting page

namespace Workdo\RecurringInvoiceBill\Http\Controllers\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($settings)
    {
        return view('recurring-invoice-bill::company.settings.index',compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('invoice bill recurring')) {

            $post = $request->all();
            unset($post['_token']);
            if($request->has('recurring_invoice_bill')){
                foreach ($post as $key => $value) {
                    $data = [
                        'key' => $key,
                        'workspace' => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
            else
            {
               $data = [
                    'key' => 'recurring_invoice_bill',
                    'workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                Setting::updateOrInsert($data, ['value' => 'off']);

            }

            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', 'Recurring Invoice/Bill setting save sucessfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
