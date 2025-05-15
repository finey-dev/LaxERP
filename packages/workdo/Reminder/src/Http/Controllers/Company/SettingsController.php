<?php
// This file use for handle company setting page

namespace Workdo\Reminder\Http\Controllers\Company;
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
        return view('reminder::company.settings.index',compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('reminder manage')) {

            $post = $request->all();
            unset($post['_token']);
            if($request->has('reminder_notification_is')){
                foreach ($post as $key => $value) {
                    // Define the data to be updated or inserted
                    $data = [
                        'key' => $key,
                        'workspace' => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                    // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
            else
            {
                  // Define the data to be updated or inserted
               $data = [
                    'key' => 'reminder_notification_is',
                    'workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                // Check if the record exists, and update or insert accordingly
                Setting::updateOrInsert($data, ['value' => 'off']);

            }

            // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', 'Reminder setting save sucessfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
