<?php

namespace Workdo\Requests\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Requests\Entities\RequestSubcategory;
use Workdo\Requests\Entities\RequestCategory;
use Workdo\Requests\Events\CreateRequestSubCategory;
use Illuminate\Support\Facades\Auth;
use Workdo\Requests\Events\UpdateRequestSubCategory;
use Workdo\Requests\Events\DestroyRequestSubCategory;

class RequestSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('Requests subcategory manage')) {
            $requestsubcategory = RequestSubcategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get();
            return view('requests::subcategory.index',compact('requestsubcategory'));
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
        if (Auth::user()->isAbleTo('Requests subcategory create')) {
            $requestscategory = RequestCategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');

            return view('requests::subcategory.create',compact('requestscategory'));
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
        if (Auth::user()->isAbleTo('Requests subcategory create')) {
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
            $requestsubscategory               = new RequestSubcategory();
            $requestsubscategory->name         = $request->name;
            $requestsubscategory->category_id  = $request->category_id;
            $requestsubscategory->created_by   = creatorId();
            $requestsubscategory->workspace   = getActiveWorkSpace();
            $requestsubscategory->save();
            event(new CreateRequestSubCategory($request,$requestsubscategory));

            return redirect()->back()->with('success', __('The subcategory has been created successfully.'));
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
        if (Auth::user()->isAbleTo('Requests subcategory edit')) {

            $requestsubscategory = RequestSubcategory::find($id);
            $requestscategory = RequestCategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');

            return view('requests::subcategory.edit',compact('requestscategory','requestsubscategory'));
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
        if (Auth::user()->isAbleTo('Requests subcategory edit')) {
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
            $requestsubscategory               = RequestSubcategory::find($id);
            $requestsubscategory->name         = $request->name;
            $requestsubscategory->category_id  = $request->category_id;
            $requestsubscategory->save();
            event(new UpdateRequestSubCategory($request,$requestsubscategory));

            return redirect()->back()->with('success', __('The subcategory details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('Requests subcategory delete')) {

            $RequestSubcategory = RequestSubcategory::find($id);
            $RequestSubcategory->delete();
            event(new DestroyRequestSubCategory($RequestSubcategory));

            return redirect()->back()->with('success', __('The subcategory has been deleted.'));
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }
}
