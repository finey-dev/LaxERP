<?php

namespace Workdo\Requests\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Requests\Entities\Requests;
use Workdo\Requests\Entities\RequestFormField;
use Workdo\Requests\Events\CreateRequestFormField;
use Workdo\Requests\Events\UpdateRequestFormField;
use Workdo\Requests\Events\DestroyRequestFormField;
use Illuminate\Support\Facades\Auth;
use Workdo\Requests\DataTables\RequestFormFieldDatatable;
class RequestFormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id)
    {
        if (Auth::user()->isAbleTo('Requests formfield manage')) {
            return view('requests::form_field.index');
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
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('Requests formfield create')) {
            $dataTable = new RequestFormFieldDatatable($id);
            $Requests = Requests::find($id);
            if($Requests){
                return $dataTable->render('requests::form_field.index',compact('Requests'));
            }else{
                return redirect()
                ->back()
                ->with('error', __('FormField not found.'));
            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('Requests formfield edit')) {

                $RequestFormField   = RequestFormField::$type;
                $FormField          = RequestFormField::find($id);
                if($FormField){
                    return view('requests::form_field.edit',compact('RequestFormField','FormField'));
                }else{
                    return redirect()
                    ->back()
                    ->with('error', __('FormField not found.'));
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
        if (Auth::user()->isAbleTo('Requests formfield edit')) {
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
            $Requests                       = RequestFormField::find($id);
            if($Requests){

                $Requests->name                 = $request->name;
                $Requests->type                 = $request->type;
                $Requests->save();
                event(new UpdateRequestFormField($request,$Requests));

                return redirect()->back()->with(['success'=> 'The field has been changed successfully']);
            }
            else{
                return redirect()
                ->back()
                ->with('error', __('FormField not found.'));
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
        if (Auth::user()->isAbleTo('Requests formfield delete')) {

        $RequestFormField   = RequestFormField::find($id);
        event(new DestroyRequestFormField($RequestFormField));
        $RequestFormField->delete();
        return redirect()->back()->with(['success'=> 'The field has been deleted']);
    } else {
        return redirect()
            ->back()
            ->with('error', __('Permission denied.'));
    }


    }


    public function formfield_create(Request $request,$id){
        if (Auth::user()->isAbleTo('Requests formfield create')) {

            $Requests           = Requests::find($id);
            $RequestFormField   = RequestFormField::$type;

            return view('requests::form_field.create',compact('Requests','RequestFormField'));
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }

    public function formfield_store(Request $request){
        if (Auth::user()->isAbleTo('Requests formfield create')) {
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
            $Requests                       = new RequestFormField();
            $Requests->request_id           = $request->request_id;
            $Requests->name                 = $request->name;
            $Requests->type                 = $request->type;
            $Requests->created_by           = creatorId();
            $Requests->workspace            = getActiveWorkSpace();
            $Requests->save();
            event(new CreateRequestFormField($request,$Requests));

            return redirect()->back()->with(['success'=> 'The field has been created successfully']);
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }
}
