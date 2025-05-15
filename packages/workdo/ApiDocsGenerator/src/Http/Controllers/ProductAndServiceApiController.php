<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\ProductService\Entities\Category;
use Workdo\ProductService\Entities\ProductService;
use Workdo\ProductService\Entities\Tax;
use Workdo\ProductService\Entities\Unit;
use Workdo\ProductService\Events\CreateCategory;
use Workdo\ProductService\Events\CreateTax;
use Workdo\ProductService\Events\CreateUnit;
use Workdo\ProductService\Events\DestroyCategory;
use Workdo\ProductService\Events\DestroyTax;
use Workdo\ProductService\Events\DestroyUnit;
use Workdo\ProductService\Events\UpdateCategory;
use Workdo\ProductService\Events\UpdateTax;
use Workdo\ProductService\Events\UpdateUnit;

class ProductAndServiceApiController extends Controller
{
    public function categoryList(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $productCategories = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id',$request->workspace_id)->where('type',0)->get()
            ->map(function($productCategory){
                return [
                    'id'=>$productCategory->id,
                    'name'=>$productCategory->name,
                    'color'=>$productCategory->color
                ];
            });
        return response()->json(['status'=>'success','data'=>$productCategories]);
    }


    public function categoryStore(Request $request)
    {
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category             = new Category();
        $category->name       = $request->name;
        $category->color      = $request->color;
        $category->created_by = creatorId();
        $category->workspace_id =  $request->workspace_id;
        $category->save();

        event(new CreateCategory($request,$category));
        return response()->json(['status'=>'success', 'message'=>'Category successfully created!']);
    }

    public function categoryUpdate(Request $request, $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        $category->name  = $request->name;
        $category->color = $request->color;
        $category->save();
        event(new UpdateCategory($request,$category));

        return response()->json(['status'=>'success','message'=>'Category successfully updated!']);
    }

    public function categoryDelete(Request $request, $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        event(new DestroyCategory($category));
        $category->delete();

        return response()->json(['status'=>'success','message'=>'Category successfully deleted!']);

    }

    public function invoiceCategoryList(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $productCategories = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id',$request->workspace_id)->where('type',1)->get()
            ->map(function($productCategory){
                return [
                    'id'=>$productCategory->id,
                    'name'=>$productCategory->name,
                    'account'=>!empty($productCategory->chartAccount) ? $productCategory->chartAccount->name : '-',
                    'color'=>$productCategory->color
                ];
            });
        return response()->json(['status'=>'success','data'=>$productCategories]);
    }

    public function invoiceCategoryStore(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $chartOfAccount = ChartOfAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->chart_of_account)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not Found!!']);
        }
        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
                'chart_of_account'=>'required|numeric'
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category                   = new Category();
        $category->name             = $request->name;
        $category->color            = $request->color;
        $category->type             = 1;
        $category->chart_account_id = $request->chart_of_account;
        $category->created_by       = creatorId();
        $category->workspace_id     =  $request->workspace_id;
        $category->save();

        event(new CreateCategory($request,$category));

        return response()->json(['status'=>'success', 'message' => 'Category successfully created!']);

    }

    public function invoiceCategoryUpdate(Request $request,$id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $chartOfAccount = ChartOfAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->chart_of_account)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not Found!!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
                'chart_of_account'=>'required|numeric'
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Invoice Category Not Found!']);
        }
        $category->name  = $request->name;
        $category->color = $request->color;
        $category->chart_account_id = $request->chart_of_account;
        $category->save();

        event(new UpdateCategory($request,$category));

        return response()->json(['status'=>'error','message'=>'Category successfully updated!']);

    }

    public function invoiceCategoryDelete(Request $request, $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        event(new DestroyCategory($category));
        $category->delete();

        return response()->json(['status'=>'success','message'=>'Invoice Category successfully deleted!']);
    }

    public function billCategoryList(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $productCategories = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id',$request->workspace_id)->where('type',2)->get()
            ->map(function($productCategory){
                return [
                    'id'=>$productCategory->id,
                    'name'=>$productCategory->name,
                    'account'=>!empty($productCategory->chartAccount) ? $productCategory->chartAccount->name : '-',
                    'color'=>$productCategory->color
                ];
            });
        return response()->json(['status'=>'success','data'=>$productCategories]);
    }

    public function billCategoryStore(Request $request)
    {
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $chartOfAccount = ChartOfAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->chart_of_account)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not Found!!']);
        }
        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
                'chart_of_account'=>'required|numeric'
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category                   = new Category();
        $category->name             = $request->name;
        $category->color            = $request->color;
        $category->type             = 2;
        $category->chart_account_id = $request->chart_of_account;
        $category->created_by       = creatorId();
        $category->workspace_id     =  $request->workspace_id;
        $category->save();

        event(new CreateCategory($request,$category));

        return response()->json(['status'=>'success', 'message' => 'Bill Category successfully created!']);
    }

    public function billCategoryUpdate(Request $request , $id)
    {
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $chartOfAccount = ChartOfAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->chart_of_account)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not Found!!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required',
                'chart_of_account'=>'required|numeric'
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Bill Category Not Found!']);
        }
        $category->name  = $request->name;
        $category->color = $request->color;
        $category->chart_account_id = $request->chart_of_account;
        $category->save();

        event(new UpdateCategory($request,$category));

        return response()->json(['status'=>'error','message'=>'Bill Category successfully updated!']);
    }

    public function billCategoryDelete(Request $request , $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $category = Category::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->where('id',$id)->first();
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Bill Category Not Found!']);
        }

        event(new DestroyCategory($category));
        $category->delete();

        return response()->json(['status'=>'success','message'=>'Bill Category successfully deleted!']);
    }

    public function taxList(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $taxes = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->get()
            ->map(function($tax){
                return [
                    'id'    => $tax->id,
                    'name'  => $tax->name,
                    'rate'  => $tax->rate
                ];
            });
        return response()->json(['status'=>'success','data'=>$taxes]);
    }

    public function taxCreate(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'rate' => 'required|numeric',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $tax                = new Tax();
        $tax->name          = $request->name;
        $tax->rate          = $request->rate;
        $tax->created_by    = creatorId();
        $tax->workspace_id  = $request->workspace_id;
        $tax->save();

        event(new CreateTax($request,$tax));
        return response()->json(['status'=>'success', 'message' => 'Tax rate successfully created!']);

    }

    public function taxUpdate(Request $request , $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'rate' => 'required|numeric',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $tax = Tax::where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$tax){
            return response()->json(['status'=>'error','message'=>'Tax not Found!']);
        }
        $tax->name = $request->name;
        $tax->rate = $request->rate;
        $tax->save();

        event(new UpdateTax($request,$tax));
        return response()->json(['status'=>'success','message'=> 'Tax rate successfully updated!']);
    }

    public function taxDelete(Request $request, $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $tax = Tax::where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$tax){
            return response()->json(['status'=>'error','message'=>'Tax not Found!']);
        }

        event(new DestroyTax($tax));
        $tax->delete();

        return response()->json(['status'=>'success','message'=>'Tax rate successfully deleted!']);
    }

    public function unitList(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $units = \Workdo\ProductService\Entities\Unit::where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->get()
                ->map(function($unit){
                    return [
                        "id"=> $unit->id,
                        "name"=> $unit->name
                    ];
                });
        return response()->json(['status'=>'success','data'=>$units]);
    }

    public function unitStore(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $category             = new Unit();
        $category->name       = $request->name;
        $category->created_by = creatorId();
        $category->workspace_id = $request->workspace_id;
        $category->save();

        event(new CreateUnit($request,$category));

        return response()->json(['status'=>'success', 'message' => 'Unit successfully created!']);
    }

    public function unitUpdate(Request $request , $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $unit = Unit::where('id',$id)->where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->first();
        if(!$unit){
            return response()->json(['status'=>'success','message'=>'Unit Not Found!']);
        }

        $unit->name = $request->name;
        $unit->save();

        event(new UpdateUnit($request,$unit));
        return response()->json(['status'=>'success','message'=>'Unit successfully updated!']);
    }

    public function unitDelete(Request $request, $id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }
        $unit = Unit::where('id',$id)->where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->first();
        if(!$unit){
            return response()->json(['status'=>'success','message'=>'Unit Not Found!']);
        }
        event(new DestroyUnit($unit));
        $unit->delete();

        return response()->json(['status'=>'success','message'=>'Unit successfully deleted!']);
    }

    public function products(Request $request){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $productServices = ProductService::select('product_services.*', DB::raw('GROUP_CONCAT(taxes.name) as tax_names'))
            ->leftJoin('taxes', function ($join) {
                $join->on('taxes.id', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(product_services.tax_id, ',', numbers.n), ',', -1)"))
                    ->crossJoin(DB::raw('(SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) numbers'))
                    ->whereRaw('CHAR_LENGTH(product_services.tax_id) - CHAR_LENGTH(REPLACE(product_services.tax_id, ",", "")) + 1 >= numbers.n');
            })
            ->where('product_services.created_by', creatorId())
            ->where('product_services.workspace_id', $request->workspace_id)
            ->groupBy('product_services.id')
            ->with(['categorys','units'])->get()
            ->map(function($productService){
                return [
                    'id'                => $productService->id,
                    'name'              => $productService->name,
                    'sku'               => $productService->sku,
                    'sale_price'        => $productService->sale_price,
                    'purchase_price'    => $productService->purchase_price,
                    'quantity'          => $productService->quantity,
                    'image'             => get_file($productService->image),
                    'type'              => $productService->type,
                    'description'       => $productService->description,
                    'tax'               => $productService->tax_names,
                    'categorys'         => [
                        "id"     => $productService->categorys->id,
                        "name"   => $productService->categorys->name,
                        "color"  => $productService->categorys->color,
                    ],
                    "units"    => !empty($productService->units) ? [
                        "id"        => $productService->units->id,
                        "name"      => $productService->units->name,
                    ] : []
                ];
            });

        return response()->json(['status'=>'success','data'=>$productServices]);
    }

    public function showProduct(Request $request,$id){
        if (!module_is_active('ProductService')) {
            return response()->json(['status'=>'error','message'=>'Product Service Module Not Active!']);
        }

        $productService = ProductService::where('id',$id)->where('workspace_id',$request->workspace_id)->first();

        if(!$productService){
            return response()->json(['status'=>'error','message'=>'Product Not Found!']);
        }

        $data = [
            'id' => $productService->id,
            'name'=>$productService->name,
            'sku'=>$productService->sku,
            'sale_price'=>$productService->sale_price,
            'purchase_price'=>$productService->purchase_price,
            'image'=>get_file($productService->image),
            'quantity'=>$productService->quantity,
            'type'=>$productService->type,
            'description'=>$productService->description,
            'categorys'         => [
                "id"     => $productService->categorys->id,
                "name"   => $productService->categorys->name,
                "color"  => $productService->categorys->color,
            ],
            "units"             => [
                "id"        => $productService->units->id,
                "name"      => $productService->units->name,
            ]
        ];


        return response()->json(['status'=>'success','data'=>$data]);
    }
}
