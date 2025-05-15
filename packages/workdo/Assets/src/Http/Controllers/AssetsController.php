<?php

namespace Workdo\Assets\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Workdo\Assets\Entities\Asset;
use Workdo\Assets\Entities\AssetUtility;
use Workdo\Assets\Entities\AssetHistory;
use Workdo\Assets\Entities\AssetDefective;
use Workdo\Assets\Events\CreateAssets;
use Workdo\Assets\Events\DestroyAssets;
use Workdo\Assets\Events\UpdateAssets;
use Workdo\Assets\Entities\AssetsCategory;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Employee;
use Workdo\Assets\DataTables\AssetsDataTable;

class AssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(AssetsDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('assets manage')) {

            return $dataTable->render('assets::index');

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
        if (Auth::user()->isAbleTo('assets create')) {
            $branches = [];

            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'Assets')->where('sub_module', 'assets')->get();
            } else {

                $customFields = null;
            }
            $category = AssetsCategory::where('created_by',creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('assets::create', compact('customFields','branches','category'));
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
        if (Auth::user()->isAbleTo('assets create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name'            => 'required',
                    'category'        => 'required',
                    'purchase_date'   => 'required',
                    'supported_date'  => 'required',
                    'quantity'        => 'required',
                    'serial_code'     => 'required',
                    'assets_unit'     => 'required',
                    'purchase_cost'   => 'required',
                    'location'        => 'required',
                    'asset_image'     => 'required',
                    'warranty_period' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->asset_image) {

                $filenameWithExt    = time() .'_'.$request->file('asset_image')->getClientOriginalName();
                $filename           = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension          = $request->file('asset_image')->getClientOriginalExtension();
                $fileNameToStore    = $filename . '_' . time() . '.' . $extension;
                $dir                = 'assets/asset_image';
                $url                = '';
                $path               = upload_file($request,'asset_image',$filenameWithExt,$dir,[]);
                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                        return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $assets                    = new Asset();
            $assets->user_id           = $request->employee_id;
            $assets->name              = $request->name;
            $assets->category          = isset($request->category) ? $request->category :'';
            $assets->purchase_date     = $request->purchase_date;
            $assets->supported_date    = $request->supported_date;
            $assets->serial_code       = $request->serial_code;
            $assets->quantity          = $request->quantity;
            $assets->assets_unit       = $request->assets_unit;
            $assets->purchase_cost     = $request->purchase_cost;
            $assets->location          = $request->location;
            $assets->asset_image       = $filenameWithExt;
            $assets->description       = $request->description;
            $assets->warranty_period   = $request->warranty_period;
            $assets->branch            = $request->branch;
            $assets->created_by        = creatorId();
            $assets->workspace_id      = getActiveWorkSpace();
            $assets->save();

            event(new CreateAssets($request,$assets));

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($assets, $request->customField);
            }
            $success = AssetUtility::AssetQuantity($assets->id,$assets->quantity,$assets->purchase_date);

            if($success){
                return redirect()->route('asset.index')->with('success', __('The asset has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Failed to create Asset.'));
            }
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
        return redirect()->route('asset.index')->with('error', __('Permission denied.'));
        return view('assets::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('assets edit')) {
            $employees = [];

            $employees = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', Auth::user()->id)->emp()->get()->pluck('name', 'id');

            $asset = Asset::find($id);
            $category = AssetsCategory::where('created_by',creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $employee_id = $asset->user_id;
            if (module_is_active('CustomField')) {
                $asset->customField = \Workdo\CustomField\Entities\CustomField::getData($asset, 'Assets', 'assets');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Assets')->where('sub_module', 'assets')->get();
            } else {
                $customFields = null;
            }

            return view('assets::edit', compact('employees', 'asset', 'customFields','employee_id','category'));
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

        if (Auth::user()->isAbleTo('assets edit')) {
            $asset = Asset::find($id);
            if ($asset->created_by == creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name'            => 'required',
                        'category'        => 'required',
                        'purchase_date'   => 'required',
                        'supported_date'  => 'required',
                        'quantity'        => 'required',
                        'serial_code'     => 'required',
                        'assets_unit'     => 'required',
                        'purchase_cost'   => 'required',
                        'location'        => 'required',
                        'asset_image'     => 'required',
                        'warranty_period' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                if (module_is_active('Hrm')) {
                    $employees = Employee::where('user_id', '=', $request->employee_id)->first();
                }
                if (module_is_active('CustomField')) {
                    \Workdo\CustomField\Entities\CustomField::saveData($asset, $request->customField);
                }

                $asset->user_id             = $request->employee_id;
                $asset->name                = $request->name;
                $asset->category            = isset($request->category) ? $request->category :null;
                $asset->purchase_date       = $request->purchase_date;
                $asset->supported_date      = $request->supported_date;
                $asset->serial_code         = $request->serial_code;
                $asset->quantity            = $request->quantity;
                $asset->assets_unit         = $request->assets_unit;
                $asset->purchase_cost       = $request->purchase_cost;
                $asset->location            = $request->location;
                $asset->branch              = $request->branch;

                if($request->hasFile('asset_image'))
                {
                    // old file delete
                    if(!empty($asset->asset_image))
                    {
                        delete_file($asset->asset_image);
                    }

                    $filenameWithExt        = time() .'_'.$request->file('asset_image')->getClientOriginalName();
                    $filename               = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension              = $request->file('asset_image')->getClientOriginalExtension();
                    $fileNameToStore        = $filename . '_' . time() . '.' . $extension;
                    $dir                    = 'assets/asset_image';
                    $url                    = '';
                    $path                   = upload_file($request,'asset_image',$filenameWithExt,$dir,[]);

                    $asset->asset_image         = $filenameWithExt;

                }
                $asset->warranty_period     = $request->warranty_period;
                $asset->description         = $request->description;
                $asset->save();

                event(new UpdateAssets($request,$asset));

                return redirect()->route('asset.index')->with('success', __('The asset details are updated successfully.'));
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
    public function destroy($id)
    {
        if (\Auth::user()->isAbleTo('assets delete')) {

            $asset = Asset::find($id);
            $asset_history = AssetHistory::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->where('assets_id',$asset->id)->get();
            $asset_defective = AssetDefective::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->where('asset_id',$asset->id)->get();
            foreach ($asset_defective as $asset_def) {
                $asset_def->delete();
            }
            foreach ($asset_history as $asset_his) {
                $asset_his->delete();
            }
            $asset_image =  'uploads/assets/asset_image/'.$asset->asset_image ;
            if (File::exists($asset_image)) {
                File::delete($asset_image);
            }
            event(new DestroyAssets($asset_history));
            $asset->delete();
            return redirect()->back()->with('success', __('The asset has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function fileImportExport()
    {
        if (Auth::user()->isAbleTo('assets import')) {
            return view('assets::import');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function fileImport(Request $request)
    {
        if (Auth::user()->isAbleTo('assets import')) {
            session_start();

            $error = '';
            $html = '';

            if ($request->file->getClientOriginalName() != '') {
                $file_array = explode(".", $request->file->getClientOriginalName());

                $extension = end($file_array);
                if ($extension == 'csv') {
                    $file_data = fopen($request->file->getRealPath(), 'r');

                    $file_header = fgetcsv($file_data);
                    $html .= '<table class="table table-bordered"><tr>';

                    for ($count = 0; $count < count($file_header); $count++) {
                        $html .= '
                                <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                    <option value="">Set Count Data</option>
                                    <option value="name">Asset Name</option>
                                    <option value="purchase_date">Purchase Date</option>
                                    <option value="supported_date">Supported Date</option>
                                    <option value="amount">Amount</option>
                                    <option value="description">Description</option>
                                    </select>
                                </th>
                                ';
                    }
                    $html .= '</tr>';
                    $limit = 0;
                    while (($row = fgetcsv($file_data)) !== false) {
                        $limit++;

                        $html .= '<tr>';

                        for ($count = 0; $count < count($row); $count++) {
                            $html .= '<td>' . $row[$count] . '</td>';
                        }

                        $html .= '</tr>';

                        $temp_data[] = $row;
                    }
                    $_SESSION['file_data'] = $temp_data;
                } else {
                    $error = 'Only <b>.csv</b> file allowed';
                }
            } else {

                $error = 'Please Select CSV File';
            }
            $output = array(
                'error' => $error,
                'output' => $html,
            );

            return json_encode($output);
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function fileImportModal()
    {
        if (Auth::user()->isAbleTo('assets import')) {
            return view('assets::import_modal');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function assetsImportdata(Request $request)
    {
        if (Auth::user()->isAbleTo('assets import')) {
            session_start();
            $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
            $flag = 0;
            $html .= '<table class="table table-bordered"><tr>';
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);

            $user = \Auth::user();

            foreach ($file_data as $row) {
                $asset = Asset::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->Where('name', 'like', $row[$request->name])->get();

                if ($asset->isEmpty()) {

                    try {
                        Asset::create([
                            'name' => $row[$request->name],
                            'purchase_date' => $row[$request->purchase_date],
                            'supported_date' => $row[$request->supported_date],
                            'amount' => $row[$request->amount],
                            'description' => $row[$request->description],
                            'created_by' => creatorId(),
                            'workspace_id' => getActiveWorkSpace(),
                        ]);
                    } catch (\Exception $e) {
                        $flag = 1;
                        $html .= '<tr>';

                        $html .= '<td>' . $row[$request->name] . '</td>';
                        $html .= '<td>' . $row[$request->purchase_date] . '</td>';
                        $html .= '<td>' . $row[$request->supported_date] . '</td>';
                        $html .= '<td>' . $row[$request->amount] . '</td>';
                        $html .= '<td>' . $row[$request->description] . '</td>';

                        $html .= '</tr>';
                    }
                } else {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . $row[$request->name] . '</td>';
                    $html .= '<td>' . $row[$request->purchase_date] . '</td>';
                    $html .= '<td>' . $row[$request->supported_date] . '</td>';
                    $html .= '<td>' . $row[$request->amount] . '</td>';
                    $html .= '<td>' . $row[$request->description] . '</td>';

                    $html .= '</tr>';
                }
            }

            $html .= '
                            </table>
                            <br />
                            ';
            if ($flag == 1) {

                return response()->json([
                    'html' => true,
                    'response' => $html,
                ]);
            } else {
                return response()->json([
                    'html' => false,
                    'response' => 'Data Imported Successfully',
                ]);
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
