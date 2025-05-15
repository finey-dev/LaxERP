<?php

namespace Workdo\Requests\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Requests\Entities\RequestCategory;
use Workdo\Requests\Events\CreateRequestCategory;
use Workdo\Requests\Events\UpdateRequestCategory;
use Workdo\Requests\Events\DestroyRequestCategory;
use Illuminate\Support\Facades\Auth;

class RequestCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('Requests category manage')) {

        $requestscategory = RequestCategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get();
        return view('requests::category.index',compact('requestscategory'));
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('Requests category create')) {
          return view('requests::category.create');
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('Requests category create')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required|max:120',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $requestscategory = new RequestCategory();
            $requestscategory->name = $request->name;
            $requestscategory->created_by = creatorId();
            $requestscategory->workspace = getActiveWorkSpace();
            $requestscategory->save();
            event(new CreateRequestCategory($request,$requestscategory));

            return redirect()->back()->with('success', __('The category has been created successfully.'));
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('requests::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('Requests category edit')) {

        $requestscategory = RequestCategory::find($id);
        if($requestscategory){
            return view('requests::category.edit',compact('requestscategory'));
        }else{
            return redirect()->back()->with('error', __('Category not found.'));

        }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
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
        if (Auth::user()->isAbleTo('Requests category edit')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required|max:120',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $requestscategory = RequestCategory::find($id);
            if($requestscategory){
                $requestscategory->name = $request->name;
                $requestscategory->save();
                event(new UpdateRequestCategory($request,$requestscategory));
             return redirect()->back()->with('success', __('The category details are updated successfully.'));
            }else{
                return redirect()->back()->with('error', __('Category not found.'));

            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('Requests category delete')) {

            $requestscategory =RequestCategory::find($id);
            if($requestscategory){
                $requestscategory->delete();
                event(new DestroyRequestCategory($requestscategory));

                return redirect()->back()->with('success', __('The category has been deleted.'));
            }else{
                return redirect()->back()->with('error', __('Category not found.'));

            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }



}
