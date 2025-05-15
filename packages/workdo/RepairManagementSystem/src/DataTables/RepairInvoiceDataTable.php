<?php

namespace Workdo\RepairManagementSystem\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Entities\RepairInvoice;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RepairInvoiceDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumn = ['status','invoice_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('repair_id', function (RepairInvoice $repair_movement_hostory) {
                return $repair_movement_hostory->repairOrderRequest->product_name ?? '-';
            })
            ->editColumn('invoice_id', function (RepairInvoice $repair_invoice) {
                if (\Laratrust::hasPermission('repair invoice show')) {
                    $html = "<a href=" . route('repair.request.invoice.show', \Crypt::encrypt($repair_invoice->id)) . " class='btn btn-outline-primary'>" . \Workdo\RepairManagementSystem\Entities\RepairOrderRequest::invoiceNumberFormat($repair_invoice->invoice_id) . "</a>";
                } else {
                    $html = "<a href='#' class='btn btn-outline-primary'>" . \Workdo\RepairManagementSystem\Entities\RepairOrderRequest::invoiceNumberFormat($repair_invoice->invoice_id) . "</a>";
                }
                return $html;
            })
            ->editColumn('repair_charge', function (RepairInvoice $repair_invoice) {
                $comapny_setting = getCompanyAllSetting($repair_invoice->created_by, $repair_invoice->workspace);
                if ($comapny_setting['site_currency_symbol_position'] == 'pre') {
                    $repair_charge = $repair_invoice->repair_charge ? $comapny_setting['defult_currancy_symbol'] . $repair_invoice->repair_charge : 0;
                } else {
                    $repair_charge =  $repair_invoice->repair_charge ? $repair_invoice->repair_charge . $comapny_setting['defult_currancy_symbol'] : 0;
                }
                return $repair_charge;
            })
            ->editColumn('total_amount', function (RepairInvoice $repair_invoice) {
                $comapny_setting = getCompanyAllSetting($repair_invoice->created_by, $repair_invoice->workspace);
                if ($comapny_setting['site_currency_symbol_position'] == 'pre') {
                    $total_amount = $repair_invoice->repairOrderRequest->getTotal() ? $comapny_setting['defult_currancy_symbol'] . $repair_invoice->repairOrderRequest->getTotal() : 0;
                } else {
                    $total_amount =  $repair_invoice->repairOrderRequest->getTotal() ? $repair_invoice->repairOrderRequest->getTotal() . $comapny_setting['defult_currancy_symbol'] : 0;
                }
                return $total_amount;
            })
            ->editColumn('status', function (RepairInvoice $repair_invoice) {
                if ($repair_invoice->status == 0) {
                    $html = "<span class='badge bg-warning p-2 px-3'>" . __('Pending') . "</span>";
                } else if ($repair_invoice->status == 1) {
                    $html = "<span class='badge bg-success p-2 px-3'>" . __('Partialy Paid') . "</span>";
                } else if ($repair_invoice->status == 2) {
                    $html = "<span class='badge bg-primary p-2 px-3'>" . __('Paid') . "</span>";
                }
                return $html;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos(__('Pending'), $keyword) !== false) {
                    $query->where('status', 0);
                }
                if (stripos(__('Partialy Paid'), $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
                if (stripos(__('Paid'), $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
                return $query;
            });
        if (\Laratrust::hasPermission('repair invoice show')) {
            $dataTable->addColumn('action', function (RepairInvoice $repair_invoice) {
                return view('repair-management-system::repair-invoice.action', compact('repair_invoice'));
            });
            $rawColumn[] = 'action';
        }
        return  $dataTable->rawColumns($rawColumn);
    }

    public function query(RepairInvoice $model): QueryBuilder
    {
        return $model->with('repairOrderRequest')->where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('repair_invoice-table')
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
            Column::make('invoice_id')->title(__('Invoice')),
            Column::make('repair_id')->title(__('Product Name'))->name('repairOrderRequest.product_name')->orderable(false),
            Column::make('repair_charge')->title(__('Repair Charge')),
            Column::make('total_amount')->title(__('Total Amount')),
            Column::make('status')->title(__('Status'))
        ];
        if (\Laratrust::hasPermission('repair invoice show')) {
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

    protected function filename(): string
    {
        return 'RepairInvoices_' . date('YmdHis');
    }
}
