<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\DataTables\VisitorDatatable;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\DeleteVisitor;
use Workdo\VisitorManagement\Events\UpdateVisitor;
use Workdo\VisitorManagement\Entities\Visitlog;

class VisitorsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(VisitorDatatable $dataTable)
    {
        if(\Auth::user()->isAbleTo('visitor manage')){

            return $dataTable->render('visitor-management::visitors.index');
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->isAbleTo('visitor edit')){
            $visitor = Visitors::find($id);
            if(!$visitor){
                return redirect()->back()->with('error',__('Visitor Not Found!'));
            }
            return view('visitor-management::visitors.edit',compact('visitor'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->isAbleTo('visitor edit')){
            $validator = \Validator::make(
                $request->all(), [
                    'first_name' => 'required',
                    'last_name'  => 'required',
                    'email'      => 'required|unique:visitors,email,'.$id,
                    'phone'      => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $visitor = Visitors::find($id);
            if(!$visitor){
                return redirect()->back()->with('error',__('Visitor Not Found!'));
            }
            $visitor->update($request->all());
            event(new UpdateVisitor($request,$visitor));
            return redirect()->back()->with('success', __('The Visitor details are updated successfully!.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        $visitor = Visitors::find($id);

        if(!$visitor){
            return redirect()->back()->with('error', __('Visitor Not Found!'));
        }

        Visitlog::where('visitor_id', $id)->delete();

        event(new DeleteVisitor($visitor));

        $visitor->delete();

        return redirect()->back()->with('success', __('The Visitor has been deleted!'));
    }

}
