<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\DataTables\VisitorDocumentDataTable;
use Workdo\VisitorManagement\Entities\VisitorDocument;
use Workdo\VisitorManagement\Entities\VisitorDocumentType;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\CreateVisitorDocument;
use Workdo\VisitorManagement\Events\DeleteVisitorDocument;
use Workdo\VisitorManagement\Events\UpdateVisitorDocument;

class VisitorDocumentsController extends Controller
{
    public function index(VisitorDocumentDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor documents manage')) {
            return $dataTable->render('visitor-management::visitor-document.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor documents create')) {

            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");
            $document = VisitorDocumentType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('name', 'id');
            $document->prepend("Select document Type", "");
            return view('visitor-management::visitor-document.create', compact('visitors','document'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor documents create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $visitor_document                = new VisitorDocument();
            $visitor_document->visitor_id = $request->visitor_id;
            $visitor_document->document_type = $request->document_type;
            $visitor_document->document_number = $request->document_number;
            $visitor_document->date        =  date('Y-m-d H:i:s', strtotime($request->date));
            $visitor_document->status        =  $request->status;
            $visitor_document->workspace     = getActiveWorkSpace();
            $visitor_document->created_by    = creatorId();
            $visitor_document->save();
            event(new CreateVisitorDocument($request, $visitor_document));

            return redirect()->route('visitors-documents.index')->with('success', __('The Visitor Documents has been created successfully.'));
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
        if (\Auth::user()->isAbleTo('visitor documents edit')) {
            $visitor_document     = VisitorDocument::find($id);
            if (!$visitor_document) {
                return redirect()->back()->with('error', __('Visitor visitor-document Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");
            $document = VisitorDocumentType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('name', 'id');
            $document->prepend("Select document Type", "");
            return view('visitor-management::visitor-document.edit', compact('visitor_document', 'visitors','document'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('visitor documents edit')) {

            $visitor_document = VisitorDocument::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $visitor_document->visitor_id = $request->visitor_id;
            $visitor_document->document_type = $request->document_type;
            $visitor_document->document_number = $request->document_number;
            $visitor_document->date        =  date('Y-m-d H:i:s', strtotime($request->date));
            $visitor_document->status        =  $request->status;
            $visitor_document->save();
            event(new UpdateVisitorDocument($request, $visitor_document));

            return redirect()->route('visitors-documents.index')->with('success', __(' The Visitor Documents has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('visitor documents delete')) {
            $visitor_document = VisitorDocument::find($id);
            if (!$visitor_document) {
                return redirect()->back()->with('error', __('Visitor documents Not Found'));
            }
            event(new DeleteVisitorDocument($visitor_document));
            $visitor_document->delete();
            return redirect()->back()->with('success', __('The Visitor Documents has been deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
