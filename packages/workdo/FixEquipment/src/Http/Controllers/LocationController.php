<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\EquipmentLocation;
use Workdo\FixEquipment\Events\CreateLocation;
use Workdo\FixEquipment\Events\DestroyLocation;
use Workdo\FixEquipment\Events\UpdateLocation;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('equipment location manage')){

            $locations = EquipmentLocation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::location.index', compact('locations'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('equipment location create')){
            return view('fix-equipment::location.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('equipment location create')){

            $validator = Validator::make(
                $request->all(),
                [
                    'location_name'  => 'required',
                    'address'        => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $location = new EquipmentLocation();

            if ($request->hasFile('attachment')) {

                $filenameWithExt = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request, 'attachment', $filenameWithExt, 'fix_equipment/attachment');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $location->location_name        = $request->location_name;
            $location->address              = $request->address;
            $location->location_description = $request->description;
            $location->attachment           = !empty($url) ? $url : '';
            $location->workspace            = getActiveWorkSpace();
            $location->created_by           = creatorId();
            $location->save();

            event(new CreateLocation($request, $location));

            return redirect()->back()->with('suucess', __('The location has been created successfully'));
        } else {
            return redirect()->back()->with( __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('fix-equipment::show');
    }

    public function edit($id)
    {
        if(Auth::user()->isAbleTo('equipment location edit')){

            $location = EquipmentLocation::find($id);

            return view('fix-equipment::location.edit', compact('location'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('equipment location edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'location_name'  => 'required',
                    'address'        => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $location = EquipmentLocation::find($id);

            $url = '';
            if ($request->hasFile('attachment')) {

                $old_attachment =  $location->attachment;

                if (file_exists($old_attachment)) {
                    delete_file($old_attachment);
                }

                $filenameWithExt = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request, 'attachment', $filenameWithExt, 'fix_equipment/attachment');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $location->location_name        = $request->location_name;
            $location->attachment           = !empty($url) ? $url : $location->attachment;
            $location->location_description = $request->description;
            $location->address              = $request->address;
            $location->save();

            event(new UpdateLocation($request, $location));

            return redirect()->back()->with('success', __('The location details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('equipment location delete')){

            $location = EquipmentLocation::find($id);

            if (file_exists($location->attachment)) {
                delete_file($location->attachment);
            }

            event(new DestroyLocation($location));

            $location->delete();

            return redirect()->back()->with('success', __('The location has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
