<?php

namespace Workdo\Facilities\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Workdo\Facilities\Entities\FacilitiesReceipt;
use Illuminate\Support\Facades\DB;

class ReceiptDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['service','price','total_price','tax_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('service', function (FacilitiesReceipt $facilitiesreceipt) {
                return $facilitiesreceipt->service;
            })
            ->filterColumn('service', function ($query, $keyword) {
                $query->whereHas('services', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('total_price', function (FacilitiesReceipt $facilitiesreceipt) {
                return currency_format_with_sym($facilitiesreceipt->total_price);
            })
            ->filterColumn('total_price', function ($query, $keyword) {
                $query->where('price', 'like', "%$keyword%");
            })
            ->editColumn('price', function (FacilitiesReceipt $facilitiesreceipt) {
                return currency_format_with_sym($facilitiesreceipt->sale_price * $facilitiesreceipt->person);
            })
            ->editColumn('tax_id', function (FacilitiesReceipt $facilitiesreceipt) {
                $taxes = \App\Models\Invoice::tax($facilitiesreceipt->tax_id);
                if(!empty($taxes)) {
                    $totalTaxPrice = 0;
                    foreach ($taxes as $tax) {
                        $taxPrice = \App\Models\Invoice::taxRate(
                            $tax->rate,
                            $facilitiesreceipt->sale_price,
                            $facilitiesreceipt->person,
                            0,
                        );
                        $totalTaxPrice += $taxPrice;
                        $totalPrice = $totalTaxPrice + ($facilitiesreceipt->person * $facilitiesreceipt->price);
                    }
                }
                return currency_format_with_sym($totalTaxPrice);
            });

        if (\Laratrust::hasPermission('facilities booking receipt show')) {
            $dataTable->addColumn('action', function (FacilitiesReceipt $facilitiesreceipt) {
                return view('facilities::action', compact('facilitiesreceipt'));
            });

            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FacilitiesReceipt $model): QueryBuilder
    {
        if (Auth::user()->type == 'company') {
            $facilitiesreceipt = $model
            ->select('facilities_receipts.*',
            DB::raw('CASE
                WHEN facilities_receipts.client_id > 0 THEN users.name
                ELSE facilities_receipts.name
            END as name'),
            DB::raw('CASE
                WHEN facilities_receipts.client_id > 0 THEN users.mobile_no
                ELSE facilities_receipts.number
            END as number'),
            DB::raw('facilities_receipts.price as price'),
            DB::raw('facilities_receipts.price as total_price'),
            DB::raw('facilities_bookings.person as person'),
            DB::raw('product_services.name as service'),
            DB::raw('product_services.tax_id as tax_id'),
            DB::raw('product_services.sale_price as sale_price'))

            ->leftJoin('users','users.id','facilities_receipts.client_id')
            ->leftJoin('facilities_bookings','facilities_bookings.id','facilities_receipts.booking_id')
            ->leftJoin('product_services','product_services.id','facilities_receipts.service')
            ->where('facilities_receipts.created_by', creatorId())
            ->where('facilities_receipts.workspace', getActiveWorkSpace());
        } else {
            $facilitiesreceipt = $model
            ->select('facilities_receipts.*',
            DB::raw('CASE
                WHEN facilities_receipts.client_id > 0 THEN users.name
                ELSE facilities_receipts.name
            END as name'),
            DB::raw('CASE
                WHEN facilities_receipts.client_id > 0 THEN users.mobile_no
                ELSE facilities_receipts.number
            END as number'),
            DB::raw('facilities_receipts.price as price'),
            DB::raw('facilities_receipts.price as total_price'),
            DB::raw('facilities_bookings.person as person'),
            DB::raw('product_services.name as service'),
            DB::raw('product_services.tax_id as tax_id'),
            DB::raw('product_services.sale_price as sale_price'))

            ->leftJoin('users','users.id','facilities_receipts.client_id')
            ->leftJoin('facilities_bookings','facilities_bookings.id','facilities_receipts.booking_id')
            ->leftJoin('product_services','product_services.id','facilities_receipts.service')
            ->where('facilities_receipts.client_id', Auth::user()->id)
            ->where('facilities_receipts.created_by', creatorId())
            ->where('facilities_receipts.workspace', getActiveWorkSpace());
        }
       return $facilitiesreceipt;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('facilities_receipts-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
            "dom" =>  "
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
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('service')->title(__('Service')),
            Column::make('number')->title(__('Number')),
            Column::make('gender')->title(__('Gender')),
            Column::make('price')->title(__('Price'))->searchable(false),
            Column::make('tax_id')->title(__('Tax'))->searchable(false),
            Column::make('total_price')->title(__('Total Amount')),
        ];

        if(\Laratrust::hasPermission('facilities booking receipt show')) {
            $action  = [
                Column::computed('action')->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                
            ];

            $column = array_merge($column,$action);
        }
        return $column;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'FacilitiesReceipt_' . date('YmdHis');
    }
}
