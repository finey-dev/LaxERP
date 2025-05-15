<?php

namespace Workdo\MachineRepairManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\Entities\MachineRepairRequest;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MachineRepairRequestDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['id', 'machine_id', 'customer_name', 'staff_id', 'date_of_request', 'description_of_issue', 'priority_level', 'status'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('id', function (MachineRepairRequest $repair_request) {
                return MachineRepairRequest::machineRepairNumberFormat($repair_request->id);
            })
            ->filterColumn('id', function ($query, $keyword) {
                $prefix   = !empty(company_setting('machine_repair_prefix')) ? company_setting('machine_repair_prefix') : '#MRR';
                $formattedValue = str_replace($prefix, '', $keyword);
                $query->where('id', $formattedValue);
            })
            ->editColumn('machine_id', function (MachineRepairRequest $repair_request) {
                return optional($repair_request->machine)->name ?? '-';
            })
            ->filterColumn('machine_id', function ($query, $keyword) {
                $query->whereHas('machine', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('customer_name', function (MachineRepairRequest $repair_request) {
                return $repair_request->customer_name ?? '-';
            })
            ->editColumn('staff_id', function (MachineRepairRequest $repair_request) {
                return optional($repair_request->staff)->name ?? '-';
            })
            ->filterColumn('staff_id', function ($query, $keyword) {
                $query->whereHas('staff', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('date_of_request', function (MachineRepairRequest $repair_request) {
                return company_date_formate($repair_request->date_of_request);
            })
            ->editColumn('description_of_issue', function (MachineRepairRequest $repair_request) {
                return wordwrap($repair_request->description_of_issue,100,"<br>\n");
            })
            ->editColumn('priority_level', function (MachineRepairRequest $repair_request) {
                if ($repair_request->priority_level == 'Low') {
                    $class = 'bg-primary';
                } elseif ($repair_request->priority_level == 'Medium') {
                    $class = 'bg-warning';
                } else {
                    $class = 'bg-danger';
                }
                return '<span class="badge ' . $class . ' p-2 px-3">' . $repair_request->priority_level . '</span>';
            })
            ->editColumn('status', function (MachineRepairRequest $repair_request) {
                if ($repair_request->status == 'Completed') {
                    $class = 'bg-primary';
                } elseif ($repair_request->status == 'In Progress') {
                    $class = 'bg-warning';
                } else {
                    $class = 'bg-danger';
                }
                return '<span class="badge ' . $class . ' p-2 px-3">' . $repair_request->status . '</span>';
            });
            if (\Laratrust::hasPermission('repair request show') ||
                \Laratrust::hasPermission('repair request edit') ||
                \Laratrust::hasPermission('repair request delete')) {
                    $dataTable->addColumn('action', function (MachineRepairRequest $repair_request) {
                        return view('machine-repair-management::repair-request.repair_request_action', compact('repair_request'));
                    });
                    $rowColumn[] = 'action';
            }
            return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MachineRepairRequest $model): QueryBuilder
    {
       return $model->where('workspace',getActiveWorkSpace())->where('created_by', creatorId());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('machine-repair-request-table')
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
            Column::make('id')->title(__('Id')),
            Column::make('machine_id')->title(__('Machine')),
            Column::make('customer_name')->title(__('Customer')),
            Column::make('staff_id')->title(__('Staff')),
            Column::make('date_of_request')->title(__('Date of Request')),
            Column::make('priority_level')->title(__('Priority')),
            Column::make('status')->title(__('Status')),
        ];

        if (\Laratrust::hasPermission('repair request show') ||
            \Laratrust::hasPermission('repair request edit') ||
            \Laratrust::hasPermission('repair request delete')) {
            $columns[] = Column::computed('action')
                        ->title(__('Action'))
                        ->searchable(false)
                        ->orderable(false)
                        ->exportable(false)
                        ->printable(false)
                        ->width(60)

                        ->searchable(false);
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Machine_Repair_Request_' . date('YmdHis');
    }
}
