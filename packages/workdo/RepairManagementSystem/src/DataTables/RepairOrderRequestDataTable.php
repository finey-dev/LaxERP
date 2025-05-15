<?php

namespace Workdo\RepairManagementSystem\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RepairOrderRequestDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumn = ['status','repair_technician'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('repair_technician', function (RepairOrderRequest $repair_request_order) {
                return optional($repair_request_order->RepairTechnician)->name ?? '-';
            })
            ->editColumn('status', function (RepairOrderRequest $repair_request_order) {
                if ($repair_request_order->status == 0) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('Pending') . "</span>";
                } else if ($repair_request_order->status == 1) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('Start Repairing') . "</span>";
                } else if ($repair_request_order->status == 2) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('End Repairing') . "</span>";
                } else if ($repair_request_order->status == 3) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('Start Testing') . "</span>";
                } else if ($repair_request_order->status == 4) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('End Testing') . "</span>";
                } else if ($repair_request_order->status == 5) {
                    $html = "<span class='badge fix_badge bg-warning p-2 px-3'>" . __('irreparable') . "</span>";
                } else if ($repair_request_order->status == 6) {
                    $html = "<span class='badge fix_badge bg-danger p-2 px-3'>" . __('Cancel') . "</span>";
                } else if ($repair_request_order->status == 7) {
                    $html = "<span class='badge fix_badge bg-info p-2 px-3'>" . __('Invoice created') . "</span>";
                }
                return $html;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos(__('Pending'), $keyword) !== false) {
                    $query->where('status', 0);
                }
                if (stripos(__('Start Repairing'), $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
                if (stripos(__('End Repairing'), $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
                if (stripos(__('Start Testing'), $keyword) !== false) {
                    $query->orWhere('status', 3);
                }
                if (stripos(__('End Testing'), $keyword) !== false) {
                    $query->orWhere('status', 4);
                }
                if (stripos(__('irreparable'), $keyword) !== false) {
                    $query->orWhere('status', 5);
                }
                if (stripos(__('Cancel'), $keyword) !== false) {
                    $query->orWhere('status', 6);
                }
                if (stripos(__('Invoice created'), $keyword) !== false) {
                    $query->orWhere('status', 7);
                }
                return $query;
            });
        if (
            \Laratrust::hasPermission('repair part edit') ||
            \Laratrust::hasPermission('repair part create') ||
            \Laratrust::hasPermission('repair movement history show') ||
            \Laratrust::hasPermission('repair invoice payment create') ||
            \Laratrust::hasPermission('repair order request edit') ||
            \Laratrust::hasPermission('repair order request delete')
        ) {
            $dataTable->addColumn('action', function (RepairOrderRequest $repair_request_order) {
                return view('repair-management-system::repair-order-request.action', compact('repair_request_order'));
            });
            $rawColumn[] = 'action';
        }
        return  $dataTable->rawColumns($rawColumn);
    }

    public function query(RepairOrderRequest $model): QueryBuilder
    {
        return $model->newQuery()->where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('repair_order_request-table')
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

    public function getColumns(): array
    {
        $column = [
            Column::make('id')->visible(false)->searchable(false)->printable(false)->exportable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('product_name')->title(__('Product Name')),
            Column::make('customer_name')->title(__('Customer Name')),
            Column::make('customer_email')->title(__('Customer Email')),
            Column::make('customer_mobile_no')->title(__('Customer Mobile No')),
            Column::make('location')->title(__('Location')),
            Column::make('date')->title(__('Date')),
            Column::make('expiry_date')->title(__('Expiry Date')),
            Column::make('repair_technician')->title(__('Technician')),
            Column::make('status')->title(__('Status'))
        ];
        if (
            \Laratrust::hasPermission('repair part edit') ||
            \Laratrust::hasPermission('repair part create') ||
            \Laratrust::hasPermission('repair movement history show') ||
            \Laratrust::hasPermission('repair invoice payment create') ||
            \Laratrust::hasPermission('repair order request edit') ||
            \Laratrust::hasPermission('repair order request delete')
        ) {
            $action = [
                Column::computed('action')->title(__('Action'))
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)

            ];
            $column = array_merge($column,$action);
        }
        return $column;
    }

    protected function filename(): string
    {
        return 'RepairOrderRequests_' . date('YmdHis');
    }
}
