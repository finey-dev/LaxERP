<?php

namespace Workdo\Facilities\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Facilities\Entities\FacilitiesSpace;
use Illuminate\Support\Facades\Auth;
use Workdo\Facilities\Events\CreateFacilitiesSpace;
use Workdo\Facilities\Events\UpdateFacilitiesSpace;
use Workdo\Facilities\DataTables\SpacesDataTable;

class FacilitiesSpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SpacesDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('facilitiesspace manage')) {

            return $dataTable->render('facilities::space.index');
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('facilitiesspace create')){
            return view('facilities::space.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('facilitiesspace create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('facilities-space.index')->with('error', $messages->first());
            }

            $facilitiesSpace              = new FacilitiesSpace();
            $facilitiesSpace->name        = $request->name;
            $facilitiesSpace->workspace   = getActiveWorkSpace();
            $facilitiesSpace->created_by  = creatorId();
            $facilitiesSpace->save();

            event(new CreateFacilitiesSpace($request,$facilitiesSpace));

            return redirect()->route('facilities-space.index')->with('success', __('The space has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('facilities::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('facilitiesspace edit')) {
            $space  = FacilitiesSpace::where('id', $id)->where('workspace', getActiveWorkSpace())->first();
            return view('facilities::space.edit', compact('space'));
        }
        else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('facilitiesspace edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $facilitiesSpace = FacilitiesSpace::find($id);
            $facilitiesSpace['name']       = $request->name;
            $facilitiesSpace['workspace']  = getActiveWorkSpace();
            $facilitiesSpace['created_by'] = creatorId();
            $facilitiesSpace->update();

            event(new UpdateFacilitiesSpace($request,$facilitiesSpace));

            return redirect()->route('facilities-space.index')->with('success', __('The space has been updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('facilitiesspace delete'))
        {
            $facilitiesspace = FacilitiesSpace::find($id);

            if(!empty($facilitiesspace))
            {
                $facilitiesspace->delete();
                return redirect()->route('facilities-space.index')->with('success', 'The space has been deleted successfully.' );
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
