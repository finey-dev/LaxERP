<?php

namespace Workdo\TimeTracker\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\TimeTracker\Entities\TimeTracker;
use Workdo\TimeTracker\Entities\TrackPhoto;
use Illuminate\Support\Facades\Validator;
use Workdo\TimeTracker\Events\DestroyTimeTracker;
use Workdo\TimeTracker\DataTables\TimeTrackerDataTable;

class TimeTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderabl
     */
    public function index(TimeTrackerDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('timetracker manage')) {
            return $dataTable->render('time-tracker::index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy($timetracker_id)
    {
        if (Auth::user()->isAbleTo('delete timetracker')) {

            $timetrecker = TimeTracker::find($timetracker_id);
            event(new DestroyTimeTracker($timetrecker));
            $timetrecker->delete();


            return redirect()->back()->with('success', __('The time tracker has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getTrackerImages(Request $request)
    {

        $currentWorkspace = getActiveWorkSpace();
        $tracker = TimeTracker::find($request->id);
        $images = TrackPhoto::where('track_id', $request->id)->get();

        return view('time-tracker::images', compact('images', 'tracker', 'currentWorkspace'));
    }
    public function removeTrackerImages(Request $request)
    {
        if (Auth::user()->isAbleTo('timetracker img delete')) {
            $images = TrackPhoto::find($request->id);

            if ($images) {

                $url = $images->img_path;
                if ($images->delete()) {
                    // \Storage::delete($url);

                    return success_res(__('The tracker photo has been removed.'));
                } else {
                    return error_res(__('opps something wren wrong.'));
                }
            } else {
                return error_res(__('opps something wren wrong.'));
            }
        } else {
            return error_res(__('Permission denied.'));
        }
    }
    public function removeTracker(Request $request)
    {
        if (Auth::user()->isAbleTo('delete timetracker')) {
            $track = TimeTracker::find($request->input('id'));
            if ($track) {
                $track->delete();
                return success_res(__('The track has been removed.'));
            } else {
                return error_res(__('Track not found.'));
            }
        } else {
            return error_res(__('Permission denied.'));
        }
    }

    public function setting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interval_time' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        } else {

            $data = [
                'key' => 'interval_time',
                'workspace' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ];
            Setting::updateOrInsert($data, ['value' => $request->interval_time]);
            // Settings Cache forget
            comapnySettingCacheForget();

            return redirect()->back()->with('success', ' The time tracker setting has been saved.');
        }
    }
}
