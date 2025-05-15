<?php

namespace Workdo\Internalknowledge\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Internalknowledge\Entities\Book;
use Workdo\Internalknowledge\Entities\Article;
use Illuminate\Support\Facades\Auth;
use Workdo\Internalknowledge\DataTables\BookDatatable;
use Workdo\Internalknowledge\Events\CreateBook;
use Workdo\Internalknowledge\Events\DestroyBook;
use Workdo\Internalknowledge\Events\UpdateBook;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(BookDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('book manage')) {
            return $dataTable->render('internalknowledge::book.index');
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
        $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();
        if(module_is_active('CustomField')){
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'Internalknowledge')->where('sub_module','Book')->get();
        }else{
            $customFields = null;
        }
        return view('internalknowledge::book.create', compact('users','customFields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('book create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'user_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $book              = new Book();
            $book->title       = $request->title;
            $book->description = $request->description;
            $book->user_id     = implode(",", $request->user_id);
            $book->created_by  = creatorId();
            $book->workspace   = getActiveWorkSpace();
            $book->save();
            event(new CreateBook($request, $book));
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($book, $request->customField);
            }
            return redirect()->back()->with('success', __('The book has been created successfully'));
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
        $articles   = Article::where('book', $id)->get();
        $articleCounts = [];
        return view('internalknowledge::book.show', compact('articles'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Book $book)
    {
        $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();
        $selectedUserIds = explode(',', $book->user_id);
        if(module_is_active('CustomField')){
            $book->customField = \Workdo\CustomField\Entities\CustomField::getData($book, 'Internalknowledge','Book');
            $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Internalknowledge')->where('sub_module','Book')->get();
        }else{
            $customFields = null;
        }
        return view('internalknowledge::book.edit', compact('book', 'users', 'selectedUserIds','customFields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('book edit')) {
            $book = Book::find($id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'user_id' => 'required',
                    // 'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $book->title       = $request->title;
            $book->description = $request->description;
            $book->user_id     = implode(",", $request->user_id);
            $book->created_by  = creatorId();
            $book->save();
            event(new UpdateBook($request, $book));
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($book, $request->customField);
            }

            return redirect()->back()->with('success', __('The book details are updated successfully'));
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
            $book = Book::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','Internalknowledge')->where('sub_module','Book')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $book->id)->where('field_id',$customField->id)->first();
                    event(new DestroyBook($book));
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            $book->delete();

            return redirect()->back()->with('success', __('The book has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        $books = Book::where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
        $books = $books->paginate(11);

        return view('internalknowledge::book.grid', compact('books'));
    }

    public function BookSharedLink($id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
        $books = Book::find($id);
        $articles = Article::where('book', $id)->get();
        $company_id = $books->created_by;
        $workspace_id = $books->workspace;
        return view('internalknowledge::book.copy', compact('articles', 'books','company_id','workspace_id'));
    }

    public function description($id)
    {
        $book = Book::find($id);
        return view('internalknowledge::book.description', compact('book'));
    }
    
}
