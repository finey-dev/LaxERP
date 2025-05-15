<?php

namespace Workdo\Internalknowledge\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Internalknowledge\Entities\Article;
use Workdo\Internalknowledge\Entities\Book;
use Illuminate\Support\Facades\Auth;
use Workdo\Internalknowledge\DataTables\ArticleDatatable;
use Workdo\Internalknowledge\Events\CreateArticle;
use Workdo\Internalknowledge\Events\DestroyArticle;
use Workdo\Internalknowledge\Events\UpdateArticle;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ArticleDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('article manage')) {
            return $dataTable->render('internalknowledge::article.index');
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
        $books = Book::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
        if(module_is_active('CustomField')){
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'Internalknowledge')->where('sub_module','Article')->get();
        }else{
            $customFields = null;
        }
        return view('internalknowledge::article.create', compact('books','customFields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('article create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'book' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                    'content' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $article              = new Article();
            $article->book        = $request->book;
            $article->title       = $request->title;
            $article->description = $request->description;
            $article->type        = $request->type;
            $article->content     = $request->content;
            $article->post_id     = Auth::user()->id;
            $article->created_by  = creatorId();
            $article->workspace   = getActiveWorkSpace();
            $article->save();
            event(new CreateArticle($request, $article));
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($article, $request->customField);
            }
            return redirect()->back()->with('success', __('The article has been created successfully'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Article $article)
    {
        $books = Book::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
        if(module_is_active('CustomField')){
            $article->customField = \Workdo\CustomField\Entities\CustomField::getData($article, 'Internalknowledge','Article');
            $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Internalknowledge')->where('sub_module','Article')->get();
        }else{
            $customFields = null;
        }
        return view('internalknowledge::article.edit', compact('article', 'books','customFields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('article edit')) {
            $article = Article::find($id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'book' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $article->book        = $request->book;
            $article->title       = $request->title;
            $article->description = $request->description;
            $article->type        = $request->type;
            $article->content     = $request->content;
            $article->post_id     = Auth::user()->id;
            $article->created_by  = creatorId();
            $article->workspace   = getActiveWorkSpace();
            $article->save();
            event(new UpdateArticle($request, $article));
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($article, $request->customField);
            }
            return redirect()->back()->with('success', __('The article details are updated successfully'));
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
        if (\Auth::user()->isAbleTo('book delete')) {
            $article = Article::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','Internalknowledge')->where('sub_module','Article')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $article->id)->where('field_id',$customField->id)->first();
                    event(new DestroyArticle($article));
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            $article->delete();

            return redirect()->back()->with('success', __('The article has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function copyarticle($id)
    {
        $article = Article::find($id);
        $books = Book::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
        if(module_is_active('CustomField')){
            $article->customField = \Workdo\CustomField\Entities\CustomField::getData($article, 'Internalknowledge','Article');
            $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Internalknowledge')->where('sub_module','Article')->get();
        }else{
            $customFields = null;
        }

        return view('internalknowledge::article.copy', compact('article', 'books','customFields'));
    }

    public function copyarticlestore(Request $request)
    {
        if (\Auth::user()->isAbleTo('article create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'book' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $article              = new Article();
            $article->book        = $request->book;
            $article->title       = $request->title;
            $article->description = $request->description;
            $article->type        = $request->type;
            $article->content     = $request->content;
            $article->post_id     = Auth::user()->id;
            $article->created_by  = creatorId();
            $article->workspace   = getActiveWorkSpace();
            $article->save();
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($article, $request->customField);
            }
            return redirect()->back()->with('success', __('The article has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        $articles = Article::where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
        $articles = $articles->paginate(11);

        return view('internalknowledge::article.grid', compact('articles'));
    }

    public function mindmap(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'book' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $book = $request->book;
        $title = $request->title;
        $description = $request->description;
        $type = $request->type;

        $article              = new Article();
        $article->book        = $book;
        $article->title       = $title;
        $article->description = $description;
        $article->type        = $type;
        $article->post_id     = Auth::user()->id;
        $article->created_by  = creatorId();
        $article->workspace   = getActiveWorkSpace();
        $article->save();

        return response()->json([
            'status' => 200,
            'article_id' => $article->id,
        ]);
    }

    public function updateMindmap(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'book' => 'required',
                'title' => 'required',
                'description' => 'required',
                'type' => 'required',
                ]
            );
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors(),
            ]);
        }

        $article = Article::find($request->input('id'));
        $content = json_decode($article->content);


        if (!$article) {
            return response()->json([
                'status' => 404,
                'error' => 'Article not found',
            ]);
        }

        $article->book = $request->input('book');
        $article->title = $request->input('title');
        $article->description = $request->input('description');
        $article->type = $request->input('type');
        $article->post_id     = Auth::user()->id;
        $article->created_by  = creatorId();
        $article->workspace   = getActiveWorkSpace();
        $article->save();

        return response()->json([
            'status' => 200,
            'message' => 'Article updated successfully',
            'article_id' => $article->id,
            'content' => $content,
        ]);
    }

    public function mindmapIndex(Request $request, $id)
    {
        $request->session()->put('articleId', $id);
        return view('internalknowledge::article.mindmap', compact('id'));
    }

    public function mindmapSave(Request $request, $key, $id)
    {
        $data = $request->data;
        $url = $request->newurl;

        $record = Article::find($id);
        if (!empty($record)) {
            $record->content = json_encode([
                'data' => $data,
                'newurl' => $url,
            ]);

            $record->save();
            return response()->json([
                'status' => 200,
                'message' => 'Data saved successfully',
                'record_id' => $record->id,
            ]);
        }
        return response()->json([
            'status' => 404,
            'message' => 'Data not found',
            'record_id' => 0,
        ]);
    }

    public function getMindmap($articleId)
    {
        $record = Article::find($articleId);
        $content = json_decode($record->content);

        if (!empty($content) && property_exists($content, 'data')) {
            return $content->data;
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Data property not found in the content.',
            ]);
        }
    }

    public function articleSharedLink($id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
        $articles = Article::where('id', $id)->first();
        $books = Book::where('id', $articles->book)->get();
        $company_id = $articles->created_by;
        $workspace_id = $articles->workspace;
        return view('internalknowledge::article.sharedlink', compact('articles', 'books','company_id','workspace_id'));
    }

    public function showMindmap(Request $request)
    {
        $article = Article::find($request->input('id'));
        $content = json_decode($article->content);

        return response()->json([
            'status' => 200,
            'message' => '',
            'article_id' => $article->id,
            'content' => $content,
        ]);
    }

    public function description($id)
    {
        $article = Article::find($id);
        return view('internalknowledge::article.description', compact('article'));
    }

}
