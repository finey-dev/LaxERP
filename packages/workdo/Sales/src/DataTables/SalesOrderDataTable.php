<?php

namespace Workdo\Sales\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Entities\SalesOrder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesOrderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['salesorder_id','name', 'account', 'status','created_at', 'amount','user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('salesorder_id', function (SalesOrder $salesorder) {
                $html = '';
                if (\Laratrust::hasPermission('salesorder show')) {
                    $html .= '<a href="' . route('salesorder.show', $salesorder->id) . '" data-title="' . __('Sales Order Details') . '" class="btn btn-outline-primary">';
                    $html .= SalesOrder::salesorderNumberFormat($salesorder->salesorder_id);
                    $html .= '</a>';
                } else {
                    $html .= '<a href="#" data-title="' . __('Sales Order Details') . '" class="btn btn-outline-primary">';
                    $html .= SalesOrder::salesorderNumberFormat($salesorder->salesorder_id);
                    $html .= '</a>';
                }
                return $html;
            })
            ->filterColumn('salesorder_id', function ($query, $keyword) {
                $prefix   = !empty(company_setting('salesorder_prefix')) ? company_setting('salesorder_prefix') : '#SLO';
                $formattedValue = str_replace($prefix, '', $keyword);
                $query->where('salesorder_id', $formattedValue);
            })
            ->editColumn('name', function (SalesOrder $salesorder) {
                return ucfirst($salesorder->name);
            })
            ->editColumn('account', function (SalesOrder $salesorder) {

                return ucfirst(optional($salesorder->accounts)->name ?? '-');
            })
            ->filterColumn('account', function ($query, $keyword) {
                $query->whereHas('accounts', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Open', $keyword) !== false) {
                    $query->where('status', 0);
                }
                elseif (stripos('Cancelled', $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
            })
            ->editColumn('status', function (SalesOrder $salesorder) {
                $status = [
                    0 => 'bg-secondary',
                    1 => 'bg-info',
                ];

                $class = isset($status[$salesorder->status]) ? $status[$salesorder->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3" style="width: 79px;">' . SalesOrder::$status[$salesorder->status] . '</span>';
            })
            ->editColumn('created_at', function (SalesOrder $salesorder) {
                return company_date_formate($salesorder->created_at);
            })
            ->editColumn('amount', function (SalesOrder $salesorder) {
                return currency_format_with_sym($salesorder->getTotal());
            })
            ->editColumn('user_id', function (SalesOrder $salesorder) {
                return ucfirst(optional($salesorder->assign_user)->name ?? '-');
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('assign_user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('salesorder create') ||
                \Laratrust::hasPermission('salesorder show') ||
                \Laratrust::hasPermission('salesorder edit') ||
                \Laratrust::hasPermission('salesorder delete')) {
                $dataTable->addColumn('action', function (SalesOrder $salesorder) {
                    return view('sales::salesorder.salesorder_action', compact('salesorder'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesOrder $model)
    {
       return $model->where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales-order-table')
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
        $columns = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('salesorder_id')->title(__('Id')),
            Column::make('name')->title(__('Name')),
            Column::make('account')->title(__('Account')),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::make('amount')->title(__('Amount')),
            Column::make('user_id')->title(__('Assign User')),
        ];

        if (\Laratrust::hasPermission('salesorder create') ||
            \Laratrust::hasPermission('salesorder show') ||
            \Laratrust::hasPermission('salesorder edit') ||
            \Laratrust::hasPermission('salesorder delete')) {
            $columns[] = Column::computed('action')
                ->title(__('Action'))
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ;
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Sales_Orders_' . date('YmdHis');
    }
}
