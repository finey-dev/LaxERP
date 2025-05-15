<?php

namespace Workdo\SalesAgent\DataTables;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\SalesAgent\Entities\SalesAgent;
use Workdo\SalesAgent\Entities\SalesAgentPurchase;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PurchaseOrderDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['purchaseOrder_id', 'order_value', 'delivery_status', 'order_status'];
        $dataTable =  (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('purchaseOrder_id', function (SalesAgentPurchase $order) {
                return '<a href="' . route('salesagents.purchase.order.show', Crypt::encrypt($order->id)) . '" class="btn btn-outline-primary">' . SalesAgent::purchaseOrderNumberFormat($order->id) . '</a>';
            })->filterColumn('purchaseOrder_id', function ($query, $keyword) {
                $formattedValue = str_replace(!empty(company_setting('sales_agent_purchase_order_prefix')) ? company_setting('sales_agent_purchase_order_prefix') : '#PUR', '', $keyword);
                $query->where('purchaseOrder_id', $formattedValue);
            })
            ->editColumn('delivery_status', function (SalesAgentPurchase $order) {
                if ($order->delivery_status == 0) {
                    $class = 'bg-primary';
                } elseif ($order->delivery_status == 1) {
                    $class = 'bg-info';
                } elseif ($order->delivery_status == 2) {
                    $class = 'bg-secondary';
                } elseif ($order->delivery_status == 3) {
                    $class = 'bg-warning';
                } elseif ($order->delivery_status == 4) {
                    $class = 'bg-danger';
                }
                return '<span class="badge fix_badges ' . $class . ' p-2 px-3">' .  __(SalesAgentPurchase::$purchaseOrder[$order->delivery_status]) . '</span>';
            })->filterColumn('delivery_status', function ($query, $keyword) {
                if (stripos('New Order', $keyword) !== false) {
                    $query->where('delivery_status', 0);
                } elseif (stripos('Confirmed', $keyword) !== false) {
                    $query->orWhere('delivery_status', 1);
                } elseif (stripos('Delivering', $keyword) !== false) {
                    $query->orWhere('delivery_status', 2);
                } elseif (stripos('Delivered', $keyword) !== false) {
                    $query->orWhere('delivery_status', 3);
                } elseif (stripos('Canceled', $keyword) !== false) {
                    $query->orWhere('delivery_status', 4);
                }
            })
            ->editColumn('order_status', function (SalesAgentPurchase $order) {
                if ($order->order_status == 0) {
                    $class = 'bg-primary';
                } elseif ($order->order_status == 1) {
                    $class = 'bg-info';
                } elseif ($order->order_status == 2) {
                    $class = 'bg-secondary';
                } elseif ($order->order_status == 3) {
                    $class = 'bg-warning';
                } elseif ($order->order_status == 4) {
                    $class = 'bg-danger';
                }
                $html = '<span class="badge fix_badges ' . $class . ' p-2 px-3">' .  __(SalesAgentPurchase::$purchaseOrder[$order->order_status]) . '</span>';
                return $html;
            })->filterColumn('order_status', function ($query, $keyword) {
                if (stripos('New Order', $keyword) !== false) {
                    $query->where('order_status', 0);
                } elseif (stripos('Confirmed', $keyword) !== false) {
                    $query->orWhere('order_status', 1);
                } elseif (stripos('Delivering', $keyword) !== false) {
                    $query->orWhere('order_status', 2);
                } elseif (stripos('Delivered', $keyword) !== false) {
                    $query->orWhere('order_status', 3);
                } elseif (stripos('Canceled', $keyword) !== false) {
                    $query->orWhere('order_status', 4);
                }
            })
            ->editColumn('order_value', function (SalesAgentPurchase $order) {
                return  currency_format_with_sym($order->getTotal());
            });

        if (Auth::user()->type == 'company') {
            $rowColumn = array_merge($rowColumn, ['user_id', 'invoice_id']);
            $dataTable->editColumn('user_id', function (SalesAgentPurchase $order) {
                return optional($order->User)->name ?? '';
            })->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('User', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('invoice_id', function (SalesAgentPurchase $order) {
                if (empty($order->invoice_id)) {
                    $invoiceLink = '<span class="badge fix_badges bg-secondary p-2 px-3 bill_status">' . __('Not Created Yet') . '</span>';
                } else {
                    $InvNumber      = Invoice::invoiceNumberFormat($order->invoice_id);
                    $invoice_id     = \Laratrust::hasPermission('invoice show') ? route('invoice.show', Crypt::encrypt($order->invoice_id)) : '!#';
                    $invoiceLink    = '<a target="_blank" href="' . $invoice_id . '" class="text-primary">' . $InvNumber . '</a>';
                }
                return $invoiceLink;
            });
        }

        if (\Laratrust::hasPermission(['salesagent purchase delete', 'salesagent purchase show'])) {
            $rowColumn = array_merge($rowColumn, ['action']);
            $dataTable->addColumn('action', function (SalesAgentPurchase $action) {
                return view('sales-agent::purchase.action', compact('action'));
            });
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesAgentPurchase $model): QueryBuilder
    {
        $purchaseOrders = $model->with('User')->where('workspace', getActiveWorkSpace());
        if (Auth::user()->type == 'salesagent') {
            $purchaseOrders->where('created_by', '=', Auth::user()->id);
        }
        return $purchaseOrders;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales_agent_purchases-table')
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
            Column::make('purchaseOrder_id')->title(__('Order Number')),
            Column::make('order_date')->title(__('Order Date')),
            Column::computed('order_value')->title(__('Order Value')),
            Column::make('delivery_date')->title(__('Delivery Date')),
            Column::make('delivery_status')->title(__('Delivery Status')),
            Column::make('order_status')->title(__('Order Status')),
        ];
        if (Auth::user()->type == 'company') {
            array_splice($columns, 2, 0, [Column::make('user_id')->title(__('Agent'))]);
            array_splice($columns, 5, 0, [Column::make('invoice_id')->title(__('Invoice'))]);
        }
        if (\Laratrust::hasPermission(['salesagent purchase delete', 'salesagent purchase show'])) {
            $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60);
        }
        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PurchaseOrder_' . date('YmdHis');
    }
}
