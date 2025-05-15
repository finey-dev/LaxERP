<?php

namespace Workdo\Procurement\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Account\Entities\Vender;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RfxVendorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['vendor_id','balance'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('vendor_id', function ($row) {
                if (!empty($row->vendor_id))
                {
                    $html='<a href="'.route("vendors.show", \Crypt::encrypt($row["id"])).'" class="btn btn-outline-primary">'.Vender::vendorNumberFormat($row["vendor_id"]).'</a>';
                }else{
                    $html='-';
                }
                return $html;

            })
            ->editColumn('balance', function ($row) {
                return currency_format_with_sym($row->balance);
            });
        if (\Laratrust::hasPermission('vendor edit') || \Laratrust::hasPermission('vendor delete') || \Laratrust::hasPermission('vendor show')) {
            $dataTable->addColumn('action', function ($row) {
                return view('procurement::vendorOnboard.vendor_action', compact('row'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery()
            ->select('users.*', 'vendors.*', 'users.name as name', 'users.email as email', 'users.id as id', 'vendors.contact as contact','vendors.vendor_id as vendor_id')
            ->leftjoin('vendors', 'users.id', '=', 'vendors.user_id')
            ->where('users.type', 'vendor')
            ->where('users.workspace_id', getActiveWorkSpace());
        if ($request->has('name') && !empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('country') && !empty($request->country)) {
            $query->where('vendors.billing_country', $request->country);
        }
        if ($request->has('state') && !empty($request->state)) {
            $query->where('vendors.billing_state', $request->state);
        }
        if ($request->has('city') && !empty($request->city)) {
            $query->where('vendors.billing_city', $request->city);
        }

        return $query;

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('rfx-vendor-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var country = $("select[name=country]").val();
                    d.country = country

                    var state = $("select[name=state]").val();
                    d.state = state

                    var city = $("select[name=city]").val();
                    d.city = city

                    var name = $("input[name=name]").val();
                    d.name = name
                }',
            ])
            ->orderBy(0)
            ->language([
                "paginate" => [
                    "next" => '<i class="ti ti-chevron-right"></i>',
                    "previous" => '<i class="ti ti-chevron-left"></i>'
                ],
                'lengthMenu' => "_MENU_" . __('Entries Per Page'),
                "searchPlaceholder" => __('Search...'),
                "search" => "",
                "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')
            ])
            ->initComplete('function() {
                var table = this;
                $("body").on("click", "#applyfilter", function() {
                    if (!$("select[name=country]").val() && !$("select[name=country]").val()=="All" && !$("input[name=name]").val() && !$("select[name=state]").val() && !$("select[name=state]").val()=="All" && !$("select[name=city]").val() && !$("select[name=city]").val()=="All") {
                        toastrs("Error!", "Please Select Atleast One Filter ", "error");
                        return;
                    }

                    $("#rfx-vendor-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=country]").val("")
                    $("select[name=state]").val("")
                    $("select[name=city]").val("")
                    $("input[name=name]").val("")
                    $("#rfx-vendor-table").DataTable().draw();
                });

                var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                searchInput.removeClass(\'form-control form-control-sm\');
                searchInput.addClass(\'dataTable-input\');
                var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
            }');

        $exportButtonConfig = [
            'extend' => 'collection',
            'className' => 'btn btn-light-secondary dropdown-toggle',
            'text' => '<i class="ti ti-download me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export"></i>',
            'buttons' => [
                [
                    'extend' => 'print',
                    'text' => '<i class="fas fa-print me-2"></i> ' . __('Print'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'csv',
                    'text' => '<i class="fas fa-file-csv me-2"></i> ' . __('CSV'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'excel',
                    'text' => '<i class="fas fa-file-excel me-2"></i> ' . __('Excel'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
            ],
        ];

        $buttonsConfig = array_merge([
            $exportButtonConfig,
            [
                'extend' => 'reset',
                'className' => 'btn btn-light-danger',
            ],
            [
                'extend' => 'reload',
                'className' => 'btn btn-light-warning',
            ],
        ]);

        $dataTable->parameters([
            "dom" => "
        <'dataTable-top'<'dataTable-dropdown page-dropdown'l><'dataTable-botton table-btn dataTable-search tb-search  d-flex justify-content-end gap-2'Bf>>
        <'dataTable-container'<'col-sm-12'tr>>
        <'dataTable-bottom row'<'col-5'i><'col-7'p>>",
            'buttons' => $buttonsConfig,
            "drawCallback" => 'function( settings ) {
                var tooltipTriggerList = [].slice.call(
                    document.querySelectorAll("[data-bs-toggle=tooltip]")
                  );
                  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                  });
                  var popoverTriggerList = [].slice.call(
                    document.querySelectorAll("[data-bs-toggle=popover]")
                  );
                  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                  });
                  var toastElList = [].slice.call(document.querySelectorAll(".toast"));
                  var toastList = toastElList.map(function (toastEl) {
                    return new bootstrap.Toast(toastEl);
                  });
            }'
        ]);

        $dataTable->language([
            'buttons' => [
                'create' => __('Create'),
                'export' => __('Export'),
                'print' => __('Print'),
                'reset' => __('Reset'),
                'reload' => __('Reload'),
                'excel' => __('Excel'),
                'csv' => __('CSV'),
            ]
        ]);

        return $dataTable;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $column = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false)->name('users.id'),
            Column::make('vendor_id')->title('No')->searchable(false)->exportable(false)->printable(false)->name('vendors.vendor_id'),
            Column::make('name')->title(__('Name'))->name('users.name'),
            Column::make('contact')->title(__('Contact'))->name('vendors.contact'),
            Column::make('email')->title(__('Email'))->name('users.email'),
            Column::make('balance')->title(__('Balance'))->name('vendors.balance'),
        ];
        if (\Laratrust::hasPermission('vendor edit') || \Laratrust::hasPermission('vendor delete') || \Laratrust::hasPermission('vendor show')) {
            $action = [
                Column::computed('action')->title(__('Action'))
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    
            ];
            $column = array_merge($column, $action);

        }
        return $column;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Rfx_Vendor_' . date('YmdHis');
    }
}
