<?php

namespace Workdo\SupportTicket\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\SupportTicket\Entities\KnowledgeBase;
use Workdo\SupportTicket\Entities\KnowledgeBaseCategory;
use Workdo\SupportTicket\Events\CreateKnowledgeBaseCategory;
use Workdo\SupportTicket\Events\DestroyKnowledgeBaseCategory;

class KnowledgebaseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('knowledgebasecategory manage')) {
            $knowledges_category = KnowledgeBaseCategory::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();
            return view('support-ticket::knowledgecategory.index', compact('knowledges_category'));
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
        if (Auth::user()->isAbleTo('knowledgebasecategory create')) {
            return view('support-ticket::knowledgecategory.create');
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

        if (\Auth::user()->isAbleTo('knowledgebasecategory create')) {
            $user = \Auth::user();
            $validation = [
                'title' => ['required', 'string', 'max:255'],
            ];
            $validator = \Validator::make(
                $request->all(),
                $validation
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $post = [
                'title' => $request->title,
                'workspace_id' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ];
            $KnowledgeBaseCategory=KnowledgeBaseCategory::create($post);
            event(new CreateKnowledgeBaseCategory($request, $KnowledgeBaseCategory));
            return redirect()->route('knowledge-category.index')->with('success', __('The KnowledgeBase Category has been created successfully'));
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
        return redirect()->route('knowledge-category.index')->with('error', __('Permission denied.'));

        return view('support-ticket::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $userObj = \Auth::user();
        if (Auth::user()->isAbleTo('knowledgebasecategory edit')) {
            $knowledge_category = KnowledgeBaseCategory::find($id);
            return view('support-ticket::knowledgecategory.edit', compact('knowledge_category'));
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
        $userObj = \Auth::user();
        if (Auth::user()->isAbleTo('knowledgebasecategory edit')) {
            $knowledge_category = KnowledgeBaseCategory::find($id);
            event(new DestroyKnowledgeBaseCategory($knowledge_category));
            $knowledge_category->update($request->all());
            return redirect()->route('knowledge-category.index')->with('success', __('The KnowledgeBase Category has been updated successfully'));
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
        $user = \Auth::user();
        if (Auth::user()->isAbleTo('knowledgebasecategory delete')) {
            $knowledge = KnowledgeBase::where('category', $id)->count();
            if ($knowledge == 0) {
                $knowledge_category = Knowledgebasecategory::find($id);
                $knowledge_category->delete();
                return redirect()->route('knowledge-category.index')->with('success', __('The KnowledgeBase Category has been deleted'));
            } else {
                return redirect()->back()->with('error', __('This KnowledgeBase Category is Used on Knowledge Base.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
