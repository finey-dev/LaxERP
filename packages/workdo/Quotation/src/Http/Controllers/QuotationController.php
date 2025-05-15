<?php

namespace Workdo\Quotation\Http\Controllers;

use App\Events\CreateInvoice;
use DateTime;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Setting;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Quotation\DataTables\QuotationDataTable;
use Workdo\Quotation\Entities\Quotation;
use Workdo\Quotation\Entities\QuotationProduct;
use Workdo\Quotation\Events\CreateQuotation;
use Workdo\Quotation\Events\DestroyQuotation;
use Workdo\Quotation\Events\UpdateQuotation;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(QuotationDataTable $dataTable)
    {

        if (Auth::user()->isAbleTo('quotation manage')) {
            return $dataTable->render('quotation::quotation.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request, $customerId)
    {
        if (Auth::user()->isAbleTo('quotation create')) {
            $customers   = User::where('workspace_id', '=', getActiveWorkSpace())->where('type', 'Client')->get()->pluck('name', 'id');
            $quotation_number = Quotation::quotationNumberFormat($this->quotationNumber());
            $warehouse = Warehouse::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $warehouse->prepend('Select Warehouse', '');
            $projects = [];
            $taxs = [];
            if (module_is_active('Taskly')) {
                if (module_is_active('ProductService')) {
                    $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                }
                $projects = \Workdo\Taskly\Entities\Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', Auth::user()->id)->where('workspace', getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
            }
            return view('quotation::quotation.create', compact('customers', 'customerId', 'quotation_number', 'warehouse', 'projects', 'taxs'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('quotation create')) {
            if ($request->quotation_type == "product") {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'customer_id' => 'required',
                        'quotation' => 'required',
                        'warehouse_id' => 'required',
                        'items' => 'required',
                        'quotation_date' => 'required',

                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                foreach ($request->items as $item) {

                    if (empty($item['item']) && $item['item'] == 0) {
                        return redirect()->back()->with('error', __('Please select an item'));
                    }
                }

                $quotation                 = new Quotation();
                $quotation->quotation_id    = $this->quotationNumber();
                $quotation->customer_id    = $request->customer_id;
                $quotation->account_type   = $request->account_type;
                $quotation->quotation   = $request->quotation;
                $quotation->quotation_date     = $request->quotation_date;
                $quotation->warehouse_id    = $request->warehouse_id;
                $quotation->quotation_template    = $request->quotation_template;
                $quotation->workspace       = getActiveWorkSpace();
                $quotation->created_by      = creatorId();
                $quotation->save();
                Invoice::starting_number($quotation->quotation_id + 1, 'quotation');


                $products = $request->items;
                for ($i = 0; $i < count($products); $i++) {
                    $quotationProduct                = new QuotationProduct();
                    $quotationProduct->quotation_id   = $quotation->id;
                    $quotationProduct->product_type  = $products[$i]['product_type'];
                    $quotationProduct->product_id    = $products[$i]['item'];
                    $quotationProduct->quantity      = $products[$i]['quantity'];
                    $quotationProduct->tax           = $products[$i]['tax'];
                    $quotationProduct->discount      = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $quotationProduct->price         = $products[$i]['price'];
                    $quotationProduct->description   = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);
                    $quotationProduct->save();
                }
                // first parameter request second parameter quotation
                event(new CreateQuotation($request, $quotation));
                return redirect()->route('quotation.index')->with('success', __('The quotation has been created successfully.'));
            } else if ($request->quotation_type == "project") {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'customer_id' => 'required',
                        'quotation_date' => 'required',
                        'quotation' => 'required',
                        'warehouse_id' => 'required',
                        'project' => 'required',
                        'tax_project' => 'required',
                        'items' => 'required',
                    ]

                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $quotation                 = new Quotation();
                if (module_is_active('Account')) {
                    $customer = \Workdo\Account\Entities\Customer::where('user_id', '=', $request->customer_id)->first();
                    $quotation->customer_id    = !empty($customer) ?  $customer->id : null;
                }

                $quotation->quotation_id    = $this->quotationNumber();
                $quotation->customer_id     = $request->customer_id;
                $quotation->account_type   = $request->account_type;
                $quotation->quotation       = $request->quotation;
                $quotation->quotation_date  = $request->quotation_date;
                $quotation->quotation_module = 'taskly';
                $quotation->category_id     = $request->project;
                $quotation->warehouse_id    = $request->warehouse_id;
                $quotation->quotation_template    = $request->quotation_template;
                $quotation->workspace       = getActiveWorkSpace();
                $quotation->created_by      = Auth::user()->id;

                $quotation->save();
                $products = $request->items;

                Invoice::starting_number($quotation->quotation_id + 1, 'quotation');


                $project_tax = implode(',', $request->tax_project);

                for ($i = 0; $i < count($products); $i++) {
                    $quotationProduct               = new QuotationProduct();
                    $quotationProduct->quotation_id  = $quotation->id;
                    $quotationProduct->product_id   = $products[$i]['product_id'];
                    $quotationProduct->quantity     = 1;
                    $quotationProduct->tax          = $project_tax;
                    $quotationProduct->discount     = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $quotationProduct->price        = $products[$i]['price'];
                    $quotationProduct->description  = $products[$i]['description'];
                    $quotationProduct->save();
                }
                // first parameter request second parameter quotation
                event(new CreateQuotation($request, $quotation));

                return redirect()->route('quotation.index', $quotation->id)->with('success', __('The quotation has been created successfully.'));
            } else if ($request->quotation_type == "parts") {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'customer_id' => 'required',
                        'quotation' => 'required',
                        'warehouse_id' => 'required',
                        'items' => 'required',
                        'quotation_date' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $quotation                 = new Quotation();
                $quotation->quotation_id    = $this->quotationNumber();
                $quotation->customer_id    = $request->customer_id;
                $quotation->account_type   = $request->account_type;
                $quotation->quotation   = $request->quotation;
                $quotation->quotation_date     = $request->quotation_date;
                $quotation->quotation_module = 'cmms';
                $quotation->warehouse_id    = $request->warehouse_id;
                $quotation->quotation_template    = $request->quotation_template;
                $quotation->workspace       = getActiveWorkSpace();
                $quotation->created_by      = creatorId();
                $quotation->save();

                Invoice::starting_number($quotation->quotation_id + 1, 'quotation');


                $products = $request->items;
                for ($i = 0; $i < count($products); $i++) {
                    $quotationProduct                = new QuotationProduct();
                    $quotationProduct->quotation_id   = $quotation->id;
                    $quotationProduct->product_type  = $products[$i]['product_type'];
                    $quotationProduct->product_id    = $products[$i]['product_id'];
                    $quotationProduct->quantity      = $products[$i]['quantity'];
                    $quotationProduct->tax           = $products[$i]['tax'];
                    $quotationProduct->discount      = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $quotationProduct->price         = $products[$i]['price'];
                    $quotationProduct->description   = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);
                    $quotationProduct->save();
                }
                // first parameter request second parameter quotation
                event(new CreateQuotation($request, $quotation));
                return redirect()->route('quotation.index')->with('success', __('The quotation has been created successfully.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */


    public function show($e_id)
    {
        if (Auth::user()->isAbleTo('quotation show')) {
            try {
                $id       = Crypt::decrypt($e_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Quotation Not Found.'));
            }
            $quotation = Quotation::find($id);
            if (!empty($quotation) && $quotation->workspace == getActiveWorkSpace()) {
                $user = $quotation->customer;
                $customer = [];
                if (module_is_active('Account') && !empty($user->id)) {
                    $customer    = \Workdo\Account\Entities\Customer::where('user_id', $user->id)->where('workspace', getActiveWorkSpace())->first();
                }
                $iteams   = $quotation->items;
                return view('quotation::quotation.show', compact('quotation', 'customer', 'iteams'));
            } else {
                return redirect()->route('quotation.index')->with('error', __('Quotation Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function edit($e_id)
    {

        if (Auth::user()->isAbleTo('quotation edit')) {
            try {
                $id       = Crypt::decrypt($e_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Quotation Not Found.'));
            }
            $quotation        = Quotation::find($id);
            if ($quotation->workspace == getActiveWorkSpace()) {
                $quotation_number = Quotation::quotationNumberFormat($quotation->quotation_id);
                $customers       = User::where('workspace_id', '=', getActiveWorkSpace())->where('type', 'Client')->get()->pluck('name', 'id');

                $warehouse = Warehouse::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
                $warehouse->prepend('Select Warehouse', '');

                $items = [];
                $taxs = [];
                $projects = [];
                if (module_is_active('Taskly')) {
                    if (module_is_active('ProductService')) {
                        $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                    }
                    $projects = \Workdo\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
                }
                foreach ($quotation->items as $quotationItem) {
                    $itemAmount               = $quotationItem->quantity * $quotationItem->price;
                    $quotationItem->itemAmount = $itemAmount;
                    $quotationItem->taxes      = Quotation::tax($quotationItem->tax);
                    $items[]                  = $quotationItem;
                }


                return view('quotation::quotation.edit', compact('customers', 'quotation_number',  'items', 'taxs', 'quotation', 'warehouse', 'projects'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
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
    public function update(Request $request, Quotation $quotation)
    {
        if (Auth::user()->isAbleTo('quotation edit')) {
            if ($quotation->workspace == getActiveWorkSpace()) {
                if ($request->quotation_type == "product") {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'customer_id' => 'required',
                            'quotation' => 'required',
                            'warehouse_id' => 'required',
                            'items' => 'required',
                            'quotation_date' => 'required',

                        ]
                    );
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('quotation.index')->with('error', $messages->first());
                    }
                    $quotation->customer_id        = $request->customer_id;
                    $quotation->quotation_date     = $request->quotation_date;
                    $quotation->category_id        = 0;
                    $quotation->quotation          = $request->quotation;
                    $quotation->warehouse_id       = $request->warehouse_id;
                    $quotation->account_type       = $request->account_type;
                    $quotation->quotation_module   = 'account';
                    $quotation->quotation_template    = $request->quotation_template;
                    $quotation->save();

                    $products = $request->items;

                    for ($i = 0; $i < count($products); $i++) {
                        $quotationProduct = QuotationProduct::find($products[$i]['id']);
                        if ($quotationProduct == null) {
                            $quotationProduct                = new QuotationProduct();
                            $quotationProduct->quotation_id   = $quotation->id;
                        }

                        if (isset($products[$i]['item'])) {
                            $quotationProduct->product_id    = $products[$i]['item'];
                        }
                        $quotationProduct->product_type      = $products[$i]['product_type'];
                        $quotationProduct->quantity          = $products[$i]['quantity'];
                        $quotationProduct->tax               = $products[$i]['tax'];
                        $quotationProduct->discount          = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $quotationProduct->price             = $products[$i]['price'];
                        $quotationProduct->description       = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);
                        $quotationProduct->save();
                    }

                    // first parameter request second parameter quotation
                    event(new UpdateQuotation($request, $quotation));

                    return redirect()->route('quotation.index')->with('success', __('The quotation has been updated successfully.'));
                } else if ($request->quotation_type == "project") {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'customer_id' => 'required',
                            'quotation' => 'required',
                            'warehouse_id' => 'required',
                            'quotation_date' => 'required',
                            'quotation' => 'required',
                            'project' => 'required',
                            'tax_project' => 'required',
                            'items' => 'required',
                        ]
                    );
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }

                    if (module_is_active('Account')) {
                        $customer = \Workdo\Account\Entities\Customer::where('user_id', '=', $request->customer_id)->first();
                        $quotation->customer_id    = !empty($customer) ?  $customer->id : null;
                    }
                    if ($request->quotation_type != $quotation->quotation_module) {
                        QuotationProduct::where('quotation_id', '=', $quotation->id)->delete();
                    }


                    $quotation->quotation_id        = $quotation->quotation_id;
                    $quotation->customer_id        = $request->customer_id;
                    $quotation->quotation_date     = $request->quotation_date;
                    $quotation->quotation          = $request->quotation;
                    $quotation->account_type   = $request->account_type;
                    $quotation->warehouse_id       = $request->warehouse_id;
                    $quotation->category_id        = $request->project;
                    $quotation->quotation_module    = 'taskly';
                    $quotation->quotation_template    = $request->quotation_template;
                    $quotation->save();




                    $products = $request->items;

                    $project_tax = implode(',', $request->tax_project);
                    for ($i = 0; $i < count($products); $i++) {
                        $quotationProductProduct = QuotationProduct::find($products[$i]['id']);
                        if ($quotationProductProduct == null) {
                            $quotationProductProduct             = new QuotationProduct();
                            $quotationProductProduct->quotation_id = $quotation->id;
                        }
                        $quotationProductProduct->product_id  = $products[$i]['product_id'];
                        $quotationProductProduct->quantity    = 1;
                        $quotationProductProduct->tax         = $project_tax;
                        $quotationProductProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $quotationProductProduct->price       = $products[$i]['price'];
                        $quotationProductProduct->description = $products[$i]['description'];
                        $quotationProductProduct->save();
                    }
                    // first parameter request second parameter quotation
                    event(new UpdateQuotation($request, $quotation));
                } else if ($request->quotation_type == "parts") {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'customer_id' => 'required',
                            'quotation' => 'required',
                            'warehouse_id' => 'required',
                            'items' => 'required',
                            'quotation_date' => 'required',
                        ]
                    );

                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('quotation.index')->with('error', $messages->first());
                    }

                    $quotation->customer_id        = $request->customer_id;
                    $quotation->quotation_date     = $request->quotation_date;
                    $quotation->category_id        = 0;
                    $quotation->quotation          = $request->quotation;
                    $quotation->warehouse_id       = $request->warehouse_id;
                    $quotation->account_type       = $request->account_type;
                    $quotation->quotation_module   = 'cmms';
                    $quotation->quotation_template    = $request->quotation_template;
                    $quotation->save();


                    $products = $request->items;

                    for ($i = 0; $i < count($products); $i++) {
                        $quotationProduct = QuotationProduct::find($products[$i]['id']);
                        if ($quotationProduct == null) {
                            $quotationProduct                = new QuotationProduct();
                            $quotationProduct->quotation_id   = $quotation->id;
                        }

                        if (isset($products[$i]['item'])) {
                            $quotationProduct->product_id    = $products[$i]['product_id'];
                        }
                        $quotationProduct->product_type      = $products[$i]['product_type'];
                        $quotationProduct->quantity          = $products[$i]['quantity'];
                        $quotationProduct->tax               = $products[$i]['tax'];
                        $quotationProduct->discount          = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $quotationProduct->price             = $products[$i]['price'];
                        $quotationProduct->description       = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);
                        $quotationProduct->save();
                    }

                    // first parameter request second parameter quotation
                    event(new UpdateQuotation($request, $quotation));
                    return redirect()->route('quotation.index')->with('success', __('The quotation has been updated successfully.'));
                }
                return redirect()->route('quotation.index')->with('success', __('The quotation has been updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Quotation $quotation)
    {
        if (Auth::user()->isAbleTo('quotation delete')) {
            if ($quotation->workspace == getActiveWorkSpace()) {
                QuotationProduct::where('quotation_id', '=', $quotation->id)->delete();
                // first parameter quotation
                event(new DestroyQuotation($quotation));
                $quotation->delete();

                return redirect()->route('quotation.index')->with('success', __('The quotation has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function QuotationSectionGet(Request $request)
    {

        $type = $request->type;
        $acction = $request->acction;
        $quotation = [];
        if ($acction == 'edit') {
            $quotation = Quotation::find($request->quotation_id);
        }
        if ($request->type == "product" && module_is_active('Account')) {

            $product_services = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->where('warehouse_id', $request->warehouse)->get()->pluck('name', 'id');
            $product_services_count = $product_services->count();


            $product_type['product'] = 'Product';
            $returnHTML = view('quotation::quotation.section', compact('product_services', 'product_type', 'type', 'acction', 'quotation', 'product_services_count'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } elseif ($request->type == "project" && module_is_active('Taskly')) {
            $projects = \Workdo\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->projectonly();
            if ($request->project_id != 0) {
                $projects = $projects->where('id', $request->project_id);
            }
            $projects = $projects->first();


            $tasks = [];
            if (!empty($projects)) {

                $tasks = \Workdo\Taskly\Entities\Task::where('project_id', $projects->id)->get()->pluck('title', 'id');
                if ($acction != 'edit') {
                    $tasks->prepend('--', '');
                }
            }
            $returnHTML = view('quotation::quotation.section', compact('tasks', 'type', 'acction', 'quotation'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } elseif ($request->type == "parts" && module_is_active('CMMS')) {
            $product_services = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->where('type', 'parts')->get()->pluck('name', 'id');
            $product_services_count = $product_services->count();
            if ($acction != 'edit') {
                $product_services->prepend('--', '');
            }

            if (module_is_active('CMMS')) {
                $product_type['parts'] = 'Parts';
            }
            $returnHTML = view('quotation::quotation.section', compact('product_services', 'product_type', 'type', 'acction', 'quotation', 'product_services_count'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } else {
            return [];
        }
    }


    public function product(Request $request)
    {
        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
        $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->sale_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;

        return json_encode($data);
    }

    public function items(Request $request)
    {
        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
        $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->sale_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;

        $data['items']     = QuotationProduct::where('quotation_id', $request->quotation_id)->where('product_id', $request->product_id)->first();

        return json_encode($data);
    }



    public function productQuantity(Request $request)
    {
        $product = ProductService::find($request->item_id);
        $productquantity = 0;

        if ($product) {
            $productquantity = $product->getQuantity();
        }

        return json_encode($productquantity);
    }


    function quotationNumber()
    {
        $latest = Quotation::where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->quotation_id + 1;
    }


    function invoiceNumber()
    {
        $latest = Invoice::where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }
        return $latest->invoice_id + 1;
    }

    public function productDestroy(Request $request)
    {
        if (Auth::user()->isAbleTo('quotation product delete')) {
            $product = QuotationProduct::find($request->id);
            if ($product) {

                QuotationProduct::where('id', '=', $request->id)->delete();
                return response()->json(['success' => __('The quotation product has been deleted successfully.')]);
            }

            return response()->json(['success' => __('The product has been deleted successfully')]);
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }


    public function payquotation($quotation_id)
    {
        if (!empty($quotation_id)) {
            try {
                $id = \Illuminate\Support\Facades\Crypt::decrypt($quotation_id);
            } catch (\Throwable $th) {
                return redirect('login');
            }

            $quotation = Quotation::where('id', $id)->first();
            if (!is_null($quotation)) {
                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach ($quotation->items as $item) {
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                    $totalDiscount += $item->discount;
                    $taxes         = Quotation::tax($item->tax);
                    $itemTaxes = [];
                    foreach ($taxes as $tax) {

                        if (!empty($tax)) {
                            $taxPrice            = Quotation::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = $tax->tax_name;
                            $itemTax['tax']      = $tax->rate . '%';
                            $itemTax['price']    = currency_format_with_sym($taxPrice, $quotation->created_by);
                            $itemTaxes[]         = $itemTax;

                            if (array_key_exists($tax->name, $taxesData)) {
                                $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                            } else {
                                $taxesData[$tax->tax_name] = $taxPrice;
                            }
                        } else {
                            $taxPrice            = Quotation::taxRate(0, $item->price, $item->quantity, $item->discount);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = 'No Tax';
                            $itemTax['tax']      = '';
                            $itemTax['price']    = currency_format_with_sym($taxPrice, $quotation->created_by);
                            $itemTaxes[]         = $itemTax;

                            if (array_key_exists('No Tax', $taxesData)) {
                                $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                            } else {
                                $taxesData['No Tax'] = $taxPrice;
                            }
                        }
                    }

                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $quotation->items         = $items;
                $quotation->totalTaxPrice = $totalTaxPrice;
                $quotation->totalQuantity = $totalQuantity;
                $quotation->totalRate     = $totalRate;
                $quotation->totalDiscount = $totalDiscount;
                $quotation->taxesData     = $taxesData;
                $ownerId = $quotation->created_by;

                $users = User::where('id', $quotation->created_by)->first();

                if (!is_null($users)) {
                    \App::setLocale($users->lang);
                } else {
                    \App::setLocale('en');
                }

                $quotation    = Quotation::where('id', $id)->first();
                $customer = $quotation->customer;
                $item   = $quotation->items;

                $company_payment_setting = [];

                if (module_is_active('Account')) {
                    $customer = \Workdo\Account\Entities\Customer::where('user_id', $quotation->customer_id)->first();
                } else {
                    $customer = $quotation->customer;
                }


                $settings['quotation_shipping_display'] = company_setting('quotation_shipping_display', $quotation->created_by, $quotation->workspace);
                $company_id = $quotation->created_by;
                $workspace_id = $quotation->workspace;
                return view('quotation::quotationpay', compact('quotation', 'item', 'customer', 'users', 'company_payment_setting', 'settings', 'company_id', 'workspace_id'));
            } else {
                return abort('404', 'The Link You Followed Has Expired');
            }
        } else {
            return abort('404', 'The Link You Followed Has Expired');
        }
    }



    public function quotation($quotation_id)
    {
        $quottationId = Crypt::decrypt($quotation_id);
        $quotation   = Quotation::where('id', $quottationId)->first();
        if (module_is_active('Account')) {
            $customer         = \Workdo\Account\Entities\Customer::where('user_id', $quotation->customer_id)->first();
        } else {
            $customer         = User::where('id', $quotation->customer_id)->first();
        }

        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        foreach ($quotation->items as $product) {
            $item              = new \stdClass();
            if ($quotation->quotation_module == "taskly") {
                $item->name        = !empty($product->product()) ? $product->product()->title : '';
            } elseif ($quotation->quotation_module == "account") {
                $item->name        = !empty($product->product()) ? $product->product()->name : '';
                $item->product_type   = !empty($product->product_type) ? $product->product_type : '';
            } elseif ($quotation->quotation_module == "cmms") {
                $item->name        = !empty($product->product()) ? $product->product()->name : '';
                $item->product_type   = !empty($product->product_type) ? $product->product_type : '';
            }
            $item->quantity    = $product->quantity;
            $item->tax         = $product->tax;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            if (module_is_active('ProductService')) {
                $taxes = \Workdo\ProductService\Entities\Tax::tax($product->tax);

                $itemTaxes = [];
                if (!empty($item->tax)) {
                    $tax_price = 0;
                    foreach ($taxes as $tax) {
                        $taxPrice      = Invoice::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                        $tax_price  += $taxPrice;
                        $totalTaxPrice += $taxPrice;

                        $itemTax['name']  = $tax->name;
                        $itemTax['rate']  = $tax->rate . '%';
                        $itemTax['price'] = currency_format_with_sym($taxPrice, $quotation->created_by);
                        $itemTaxes[]      = $itemTax;


                        if (array_key_exists($tax->name, $taxesData)) {
                            $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                        } else {
                            $taxesData[$tax->name] = $taxPrice;
                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $item->tax_price = $tax_price;
                } else {
                    $item->itemTax = [];
                }

                $items[] = $item;
            }
        }
        $quotation->itemData      = $items;
        $quotation->totalTaxPrice = $totalTaxPrice;
        $quotation->totalQuantity = $totalQuantity;
        $quotation->totalRate     = $totalRate;
        $quotation->totalDiscount = $totalDiscount;
        $quotation->taxesData     = $taxesData;


        //Set your logo
        $company_logo = get_file(sidebar_logo());
        $company_settings = getCompanyAllSetting($quotation->created_by, $quotation->workspace);
        $quotation_logo = isset($company_settings['quotation_logo']) ? $company_settings['quotation_logo'] : '';
        if (isset($quotation_logo) && !empty($quotation_logo)) {
            $img  = get_file($quotation_logo);
        } else {
            $img  = $company_logo;
        }
        if ($quotation) {
            $color      = '#' . (!empty($company_settings['quotation_color']) ? $company_settings['quotation_color'] : 'ffffff');
            $font_color = User::getFontColor($color);
            $quotation_template  = (!empty($company_settings['quotation_template']) ? $company_settings['quotation_template'] : 'template1');

            $settings['site_rtl'] = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
            $settings['company_name'] = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
            $settings['company_email'] = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
            $settings['company_telephone'] = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
            $settings['company_address'] = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
            $settings['company_city'] = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
            $settings['company_state'] = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
            $settings['company_zipcode'] = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
            $settings['company_country'] = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
            $settings['registration_number'] = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
            $settings['tax_type'] = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
            $settings['vat_number'] = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
            $settings['quotation_footer_title'] = isset($company_settings['quotation_footer_title']) ? $company_settings['quotation_footer_title'] : '';
            $settings['quotation_footer_notes'] = isset($company_settings['quotation_footer_notes']) ? $company_settings['quotation_footer_notes'] : '';
            $settings['quotation_shipping_display'] = isset($company_settings['quotation_shipping_display']) ? $company_settings['quotation_shipping_display'] : '';
            $settings['quotation_template'] = isset($company_settings['quotation_template']) ? $company_settings['quotation_template'] : '';
            $settings['quotation_color'] = isset($company_settings['quotation_color']) ? $company_settings['quotation_color'] : '';
            $settings['quotation_qr_display'] = isset($company_settings['quotation_qr_display']) ? $company_settings['quotation_qr_display'] : '';

            return view('quotation::quotation.templates.' . $quotation_template, compact('quotation', 'color', 'settings', 'customer', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewQuotation($template, $color)
    {
        $quotation  = new Quotation();

        $customer                   = new \stdClass();

        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Customer Name>';
        $customer->billing_country  = '<Country>';
        $customer->billing_state    = '<State>';
        $customer->billing_city     = '<City>';
        $customer->billing_phone    = '<Customer Phone Number>';
        $customer->billing_zip      = '<Zip>';
        $customer->billing_address  = '<Address>';


        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;
            $item->description    = 'In publishing and graphic design, Lorem ipsum is a placeholder';

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $item->tax_price = 10;
            $items[]       = $item;
        }


        $quotation->quotation_id = 1;
        $quotation->issue_date = date('Y-m-d H:i:s');
        $quotation->due_date   = date('Y-m-d H:i:s');
        $quotation->itemData   = $items;

        $quotation->totalTaxPrice = 60;
        $quotation->totalQuantity = 3;
        $quotation->totalRate     = 300;
        $quotation->totalDiscount = 10;
        $quotation->taxesData     = $taxesData;

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = User::getFontColor($color);

        $company_logo = get_file(sidebar_logo());

        $company_settings = getCompanyAllSetting();

        $quotation_logo =  isset($company_settings['quotation_logo']) ? $company_settings['quotation_logo'] : '';

        if (!empty($quotation_logo)) {
            $img = get_file($quotation_logo);
        } else {
            $img          =  $company_logo;
        }
        $settings['site_rtl'] = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name'] = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_address'] = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_email'] = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone'] = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_city'] = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state'] = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode'] = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country'] = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number'] = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type'] = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number'] = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['quotation_footer_title'] = isset($company_settings['quotation_footer_title']) ? $company_settings['quotation_footer_title'] : '';
        $settings['quotation_footer_notes'] = isset($company_settings['quotation_footer_notes']) ? $company_settings['quotation_footer_notes'] : '';
        $settings['quotation_shipping_display'] = isset($company_settings['quotation_shipping_display']) ? $company_settings['quotation_shipping_display'] : '';
        $settings['quotation_template'] = isset($company_settings['quotation_template']) ? $company_settings['quotation_template'] : '';
        $settings['quotation_color'] = isset($company_settings['quotation_color']) ? $company_settings['quotation_color'] : '';
        $settings['quotation_qr_display'] = isset($company_settings['quotation_qr_display']) ? $company_settings['quotation_qr_display'] : '';

        return view('quotation::quotation.templates.' . $template, compact('quotation', 'preview', 'color', 'img', 'settings', 'customer', 'font_color'));
    }


    public function saveQuotationTemplateSettings(Request $request)
    {
        $user = Auth::user();
        if ($request->hasFile('quotation_logo')) {
            $quotation_logo         = $user->id . '_quotation_logo' . time() . '.png';

            $uplaod = upload_file($request, 'quotation_logo', $quotation_logo, 'quotation_logo');
            if ($uplaod['flag'] == 1) {
                $url = $uplaod['url'];
                $old_quotation_logo = company_setting('quotation_logo');
                if (!empty($old_quotation_logo) && check_file($old_quotation_logo)) {
                    delete_file($old_quotation_logo);
                }
            } else {
                return redirect()->back()->with('error', $uplaod['msg']);
            }
        }
        $post = $request->all();
        unset($post['_token']);

        if (isset($post['quotation_template']) && (!isset($post['quotation_color']) || empty($post['quotation_color']))) {
            $post['quotation_color'] = "ffffff";
        }
        if (isset($post['quotation_logo'])) {
            $post['quotation_logo'] = $url;
        }
        if (!isset($post['quotation_shipping_display'])) {
            $post['quotation_shipping_display'] = 'off';
        }
        if (!isset($post['quotation_qr_display'])) {
            $post['quotation_qr_display'] = 'off';
        }

        foreach ($post as $key => $value) {
            // Define the data to be updated or inserted
            $data = [
                'key' => $key,
                'workspace' => getActiveWorkSpace(),
                'created_by' => Auth::user()->id,
            ];
            // Check if the record exists, and update or insert accordingly
            Setting::updateOrInsert($data, ['value' => $value]);
        }
        // Settings Cache forget
        comapnySettingCacheForget();
        return redirect()->back()->with('success', __('The quotation Print setting has been saved sucessfully.'));

    }

    public function convertInvoice($quotation_id)
    {

        if (Auth::user()->isAbleTo('quotation convert')) {
            try {
                $id       = Crypt::decrypt($quotation_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Quotation Not Found.'));
            }
            $quotation        = Quotation::where('id','=',$id)->first();
            $latest = Invoice::where('workspace', getActiveWorkSpace())->latest()->first();
            if(!empty($latest->id)){
                $quotation->is_converted = $latest->id + 1;
            }else{
                $quotation->is_converted = 1;
            }
            $quotation->save();
            

            if (module_is_active('Account')) {
                $customer = \Workdo\Account\Entities\Customer::where('user_id', '=', $quotation->customer_id)->first();
            } else if (module_is_active('SalesAgent')) {
                $customer = \Workdo\SalesAgent\Entities\Customer::where('user_id', '=', $quotation->customer_id)->first();
            }

            if($quotation->account_type=="Accounting"){
                $account_type = "Account";
            }else if($quotation->account_type=="Parts"){
                $account_type = "CMMS";
            }else if($quotation->account_type=="Projects"){
                $account_type = "Taskly";
            }

            $date = new DateTime();
            $invoice                       = new Invoice();
            $invoice->customer_id          = !empty($customer) ?  $customer->id : null;
            $invoice->invoice_id           = $this->invoiceNumber();
            $invoice->user_id              = $quotation->customer_id;
            $invoice->status               = 0;
            $invoice->invoice_module       = $quotation->quotation_module;
            $invoice->account_type         = $account_type;
            $invoice->issue_date           = $date->format('Y-m-d');
            $invoice->due_date             = $date->modify('+1 week')->format('Y-m-d');
            $invoice->invoice_template     = $quotation->quotation_template;
            $invoice->workspace            = getActiveWorkSpace();
            $invoice->created_by           = creatorId();
            $invoice->save();

            $items = [];
            $taxs = [];
            $projects = [];
            if (module_is_active('Taskly')) {
                if (module_is_active('ProductService')) {
                    $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                }
                $projects = \Workdo\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
            }
            foreach ($quotation->items as $quotationItem) {
                $itemAmount               = $quotationItem->quantity * $quotationItem->price;
                $quotationItem->itemAmount = $itemAmount;
                $quotationItem->taxes      = Quotation::tax($quotationItem->tax);
                $products[]                  = $quotationItem;
            }

            for ($i = 0; $i < count($products); $i++) {
                $invoiceProduct                 = new InvoiceProduct();
                $invoiceProduct->invoice_id     = $invoice->id;
                $invoiceProduct->product_type     = $products[$i]['product_type'];
                $invoiceProduct->product_id     = $products[$i]['product_id'];
                $invoiceProduct->quantity       = $products[$i]['quantity'];
                $invoiceProduct->tax            = isset($products[$i]['tax']) 
                                                    ? (is_array($products[$i]['tax']) 
                                                        ? implode(',', $products[$i]['tax']) 
                                                        : $products[$i]['tax']) 
                                                    : 0; 
                $invoiceProduct->discount       = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $invoiceProduct->price          = $products[$i]['price'];
                $invoiceProduct->description          = $products[$i]['description'];
                $invoiceProduct->save();
            }

            return redirect()->back()->with('success', __('The quotation has been created into invoice successfully.'));


        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }
}
