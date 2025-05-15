<?php

namespace Workdo\Internalknowledge\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Internalknowledge\Entities\Article;
use Workdo\Internalknowledge\Entities\Book;
use Illuminate\Support\Facades\Auth;
use Workdo\Internalknowledge\DataTables\MyArticleDatatable;

class MyArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MyArticleDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('my article manage')) {
            return $dataTable->render('internalknowledge::myarticle.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function description($id)
    {
        $article = Article::find($id);
        return view('internalknowledge::myarticle.description', compact('article'));
    }
}
