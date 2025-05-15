<?php

namespace Workdo\SalesAgent\DataTables;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\SalesAgent\Entities\Customer;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InvoiceDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['invoice_id', 'due_value', 'status', 'order_status', 'action'];
        $dataTable =  (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('invoice_id', function (Invoice $invoice) {
                return '<a href="' . route('salesagent.purchase.invoice.show', Crypt::encrypt($invoice->id)) . '" class="btn btn-outline-primary">' . Invoice::invoiceNumberFormat($invoice->invoice_id) . '</a>';
            })
            ->filterColumn('invoice_id', function ($query, $keyword) {
                $formattedValue = str_replace(!empty(company_setting('invoice_prefix')) ? company_setting('invoice_prefix') : '#INV', '', $keyword);
                $query->where('invoice_id', $formattedValue);
            })
            ->editColumn('due_value', function (Invoice $invoice) {
                return currency_format_with_sym($invoice->getDue());
            })
            ->editColumn('status', function (Invoice $invoice) {
                return '<a href="' . route('salesagent.purchase.invoice.show', Crypt::encrypt($invoice->id)) . '" class="btn btn-outline-primary">' . Invoice::invoiceNumberFormat($invoice->invoice_id) . '</a>';
            })
            ->editColumn('status', function (Invoice $invoice) {
                if ($invoice->status == 0) {
                    $class = 'bg-primary';
                } elseif ($invoice->status == 1) {
                    $class = 'bg-info';
                } elseif ($invoice->status == 2) {
                    $class = 'bg-secondary';
                } elseif ($invoice->status == 3) {
                    $class = 'bg-warning';
                } elseif ($invoice->status == 4) {
                    $class = 'bg-danger';
                }
                return '<span class="badge fix_badges ' . $class . ' p-2 px-3">' .  __(Invoice::$statues[$invoice->status]) . '</span>';
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Draft', $keyword) !== false) {
                    $query->where('status', 0);
                } elseif (stripos('Sent', $keyword) !== false) {
                    $query->orWhere('status', 1);
                } elseif (stripos('Unpaid', $keyword) !== false) {
                    $query->orWhere('status', 2);
                } elseif (stripos('Partialy Paid', $keyword) !== false) {
                    $query->orWhere('status', 3);
                } elseif (stripos('Canceled', $keyword) !== false) {
                    $query->orWhere('Paid', 4);
                }
            })
            ->addColumn('action', function (Invoice $action) {
                return view('sales-agent::purchase.invoiceAction', compact('action'));
            });

        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Invoice $model ,Request $request): QueryBuilder
    {
        $user       = Auth::user();
        $workspace  = getActiveWorkSpace();
        $customer   = Customer::where('user_id', $user->id)->where('workspace', $workspace)->first();
        $inv        =  $model->where('user_id', $user->id)->where('customer_id', '=', $customer->id)->where('workspace', $workspace);
        if (!empty($request->issue_date)) {
            $date_range = explode('to', $request->issue_date);
            if (count($date_range) == 2) {
                $inv->whereBetween('issue_date', $date_range);
            } else {
                $inv->where('issue_date', $date_range[0]);
            }
        }
        if ($request->status != null) {
            $inv->where('status', $request->status);
        }
        return $inv;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('invoice-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var status = $("select[name=status]").val();
                    d.status = status

                    var issue_date = $("input[name=issue_date]").val();
                    d.issue_date = issue_date
                }',
            ])->orderBy(0)
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
                    if (!$("select[name=status]").val() && !$("input[name=issue_date]").val()) {
                        toastrs("Error!", "Please select Atleast One Filter ", "error");
                        return;
                    }

                    $("#invoice-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=status]").val("")
                    $("input[name=issue_date]").val("")
                    $("#invoice-table").DataTable().draw();
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
        return [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('invoice_id')->title(__('Invoice')),
            Column::make('issue_date')->title(__('Issue Date')),
            Column::make('due_date')->title(__('Due Date')),
            Column::computed('due_value')->title(__('Due Amount')),
            Column::make('status')->c(__('Status')),
            Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-end'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Invoice_' . date('YmdHis');
    }
}
