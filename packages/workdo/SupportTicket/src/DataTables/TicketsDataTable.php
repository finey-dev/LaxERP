<?php

namespace Workdo\SupportTicket\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\SupportTicket\Entities\Ticket;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TicketsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query)
    {
        $rawColumn = ['ticket_id', 'category', 'created_at'];
        $dataTable = (new EloquentDataTable($query))
        ->addIndexColumn()
        ->editColumn('ticket_id', function (Ticket $ticket) {
            if (\Laratrust::hasPermission('ticket show')) {
                $url = route('support-tickets.edit', $ticket->id);
            } else {
                $url = '#!';
            }
            $html = '<a class="btn btn-outline-primary" href="' . $url . '">
                            ' . $ticket->ticket_id . '
                        </a>';
            return $html;
        })
        ->editColumn('category', function (Ticket $ticket) {
            return '<span class="badge badge-white p-2 px-3 badge fix_badge"
                                            style="background: ' . $ticket->color . ';">' . $ticket->category_name . '</span>';
        })
        ->editColumn('created_at', function (Ticket $ticket) {
            return $ticket->created_at->diffForHumans();
        });

        if (
            \Laratrust::hasPermission('ticket edit') ||
            \Laratrust::hasPermission('ticket delete') ||
            \Laratrust::hasPermission('ticket show')

        ) {
            $dataTable->addColumn('action', function (Ticket $ticket) {

                return view('support-ticket::ticket.action', compact('ticket'));
            });
            $rawColumn[] = 'action';
        }
        return $dataTable->rawColumns($rawColumn);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ticket $model,Request $request)
    {
        $tickets = $model->with('workspace')->select(
            [
                'tickets.*',
                'ticket_categories.name as category_name',
                'ticket_categories.color',
            ]
        )->join('ticket_categories', 'ticket_categories.id', '=', 'tickets.category');
        if ($request->status == 'in-progress') {
            $tickets->where('status', '=', 'In Progress');
        } elseif ($request->status == 'on-hold') {
            $tickets->where('status', '=', 'On Hold');
        } elseif ($request->status == 'closed') {
            $tickets->where('status', '=', 'Closed');
        }
        $tickets = $tickets->where('tickets.workspace_id', getActiveWorkSpace())->orderBy('id', 'desc');

        return $tickets;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('tickets-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var status = $("#projects").val();
                    d.status = status
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
                        $("body").on("change", "#projects", function() {
                            $("#tickets-table").DataTable().draw();
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
        $column = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('ticket_id')->title(__('Ticket ID')),
            Column::make('account_type')->title(__('Account Type')),
            Column::make('name')->title(__('Name')),
            Column::make('email')->title(__('Email')),
            Column::make('subject')->title(__('Subject')),
            Column::make('category')->title(__('Category'))->name('ticket_categories.name'),
            Column::make('status')->title(__('Status'))->name('status'),
            Column::make('created_at')->title(__('Created')),
        ];
        if (
            \Laratrust::hasPermission('ticket show') ||
            \Laratrust::hasPermission('ticket delete') ||
            \Laratrust::hasPermission('ticket edit')
        ) {
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
        return 'Tickets_' . date('YmdHis');
    }
}
