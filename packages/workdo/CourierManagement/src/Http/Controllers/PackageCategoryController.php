<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\CourierManagement\Entities\PackageCategory;
use Workdo\CourierManagement\Events\Courierpackagecategorycreate;
use Workdo\CourierManagement\Events\Courierpackagecategoryupdate;
use Workdo\CourierManagement\Events\Courierpackagecategorydelete;



class PackageCategoryController extends Controller
{

    public function index()
    {
        if (Auth::user()->isAbleto('package category manage')) {
            $packageCategoryData = PackageCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('courier-management::package_category.index', compact('packageCategoryData'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied !!!');
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('package category create')) {
            return view('courier-management::package_category.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('package category create')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $packageCategory = new PackageCategory();
            $packageCategory->category  = $request->category;
            $packageCategory->workspace  = getActiveWorkSpace();
            $packageCategory->created_by  = creatorId();
            $packageCategory->save();
            event(new Courierpackagecategorycreate($packageCategory, $request));

            return redirect()->back()->with('success', __('The package category has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function edit(Request $request, $categoryId)
    {
        if (Auth::user()->isAbleTo('package category edit')) {
            $packageCategory = PackageCategory::where('id', $categoryId)->first();
            if ($packageCategory) {
                return view('courier-management::package_category.edit', compact('packageCategory'));
            } else {
                return redirect()->back()->with('error', __('Package Category Not Found !!!'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $categoryId)
    {
        if (Auth::user()->isAbleTo('package category edit')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $packageCategory = PackageCategory::where('id', $categoryId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($packageCategory) {
                $packageCategory->category = $request->category;
                $packageCategory->save();
                event(new Courierpackagecategoryupdate($packageCategory, $request));

                return redirect()->back()->with('success', __('The package category details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Package Category Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Request $request, $categoryId)
    {
        if (Auth::user()->isAbleTo('package category delete')) {
            $packageCategory = PackageCategory::where('id', $categoryId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($packageCategory) {
                event(new Courierpackagecategorydelete($packageCategory, $request));

                $packageCategory->delete();
                return redirect()->back()->with('success', __('The package category has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Package Category Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
