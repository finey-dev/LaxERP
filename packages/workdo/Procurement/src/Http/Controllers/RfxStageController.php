<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\Entities\RfxStage;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Events\CreateRfxStage;
use Workdo\Procurement\Events\DestroyRfxStage;
use Workdo\Procurement\Events\UpdateRfxStage;

class RfxStageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('rfxstage manage')) {
            $stages = RfxStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('order', 'asc')->get();

            return view('procurement::rfxStage.index', compact('stages'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('rfxstage create')) {
            return view('procurement::rfxStage.create');
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
        if (Auth::user()->isAbleTo('rfxstage create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfxStage = new RfxStage();
            $rfxStage->title = $request->title;
            $rfxStage->workspace = getActiveWorkSpace();
            $rfxStage->created_by = creatorId();
            $rfxStage->save();

            event(new CreateRfxStage($request, $rfxStage));

            return redirect()->back()->with('success', __('The rfx stage has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('rfxstage edit')) {
            $rfxStage = RfxStage::find($id);
            if ($rfxStage) {
                return view('procurement::rfxStage.edit', compact('rfxStage'));
            } else {
                return response()->json(['error' => __('The rfx stage is not found.')], 401);
            }

        } else {
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
        if (Auth::user()->isAbleTo('rfxstage edit')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfxStage = RfxStage::find($id);
            if ($rfxStage) {
                $rfxStage->title = $request->title;
                $rfxStage->save();

                event(new UpdateRfxStage($request, $rfxStage));

                return redirect()->back()->with('success', __('The rfx stage are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The rfx stage is not found.'));
            }

        } else {
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
        if (Auth::user()->isAbleTo('rfxstage delete')) {
            $rfxStage = RfxStage::find($id);
            if($rfxStage)
            {
                if ($rfxStage->created_by == creatorId() && $rfxStage->workspace == getActiveWorkSpace()) {
                    event(new DestroyRfxStage($rfxStage));
    
                    $rfxStage->delete();
    
                    return redirect()->back()->with('success', __('The rfx stage has been deleted.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            }else{
                return redirect()->back()->with('error', __('The rfx stage is not found.'));
            }
            
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function order(Request $request)
    {
        $post = $request->all();
        foreach ($post['order'] as $key => $item) {
            $stage = RfxStage::where('id', '=', $item)->first();
            $stage->order = $key;
            $stage->save();
        }
    }
}
