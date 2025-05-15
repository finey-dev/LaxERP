<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Events\CreateDocumentType;
use Workdo\Hrm\Events\DestroyDocumentType;
use Workdo\Hrm\Events\UpdateDocumentType;
use Workdo\VisitorManagement\Entities\VisitorDocumentType;
use Workdo\VisitorManagement\Events\CreateVisitorDocumentType;
use Workdo\VisitorManagement\Events\DestroyVisitorDocumentType;
use Workdo\VisitorManagement\Events\UpdateVisitorDocumentType;

class VisitorDocumentTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAbleTo('visitor document type manage')) {
            $document_types = VisitorDocumentType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            return view('visitor-management::document-type.index', compact('document_types'));
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
        if (Auth::user()->isAbleTo('visitor document type create')) {

            return view('visitor-management::document-type.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
         if (Auth::user()->isAbleTo('visitor document type create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // Store the validated data into the database
            $document_type = new VisitorDocumentType();
            $document_type->name          =  $request->name;
            $document_type->workspace          = getActiveWorkSpace();
            $document_type->created_by         = creatorId();
            $document_type->save();
            event(new CreateVisitorDocumentType($request, $document_type));

            return redirect()->back()->with('success', __('The Document Type created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('visitor-management::show');
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('visitor document type edit')) {
            $document_type = VisitorDocumentType::find($id);
            return view('visitor-management::document-type.edit', compact('document_type'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (Auth::user()->isAbleTo('visitor document type edit')) {
            $document_type = VisitorDocumentType::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // Store the validated data into the database
            $document_type->name          =  $request->name;
            $document_type->workspace          = getActiveWorkSpace();
            $document_type->created_by         = creatorId();
            $document_type->save();
            event(new UpdateVisitorDocumentType($request, $document_type));

            return redirect()->back()->with('success', __('The Document Type updated successfully.'));
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
        if (Auth::user()->isAbleTo('visitor document type delete')) {

            $document_type = VisitorDocumentType::find($id);
            event(new DestroyVisitorDocumentType($document_type));
            $document_type->delete();
            return redirect()->route('visitors-document-type.index')->with('success', __('The Document Type has been deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
