<?php

namespace Workdo\Recruitment\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Recruitment\Entities\JobOnBoard;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class JobOnBoardDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['application', 'job', 'type', 'account', 'location', 'applied_at', 'joining_date', 'status'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('application', function (JobOnBoard $jobOnBoards) {
                return $jobOnBoards->application ? $jobOnBoards->applications->name ?? '-' : '-';
            })
            ->editColumn('job', function (JobOnBoard $jobOnBoards) {
                return !empty($jobOnBoards->applications) ? (!empty($jobOnBoards->applications->jobs) ? $jobOnBoards->applications->jobs->title : '-') : '-';
            })
            ->editColumn('type', function (JobOnBoard $jobOnBoards) {
                return Str::ucfirst($jobOnBoards->type) ?? '-';
            })
            ->addColumn('account', function (JobOnBoard $jobOnBoards) {
                if ($jobOnBoards->type == 'internal') {
                    return $jobOnBoards->branch_id ? $jobOnBoards->branches->name ?? '-' : '-';
                } else {
                    return $jobOnBoards->user_id ? $jobOnBoards->UserName->name ?? '-' : '-';
                }
            })
            ->editColumn('location', function (JobOnBoard $jobOnBoards) {
                return !empty($jobOnBoards->applications) ? (!empty($jobOnBoards->applications->jobs) ? $jobOnBoards->applications->jobs->location : '-') : '-';
            })
            ->addColumn('applied_at', function (JobOnBoard $jobOnBoards) {
                return $jobOnBoards->application ? company_date_formate($jobOnBoards->applications->created_at) ?? '-' : '-';
            })
            ->editColumn('joining_date', function (JobOnBoard $jobOnBoards) {
                return company_date_formate($jobOnBoards->joining_date) ?? '-';
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Pending', $keyword) !== false) {
                    $query->where('status', 'pending');
                } elseif (stripos('Cancel', $keyword) !== false) {
                    $query->orWhere('status', 'cancel');
                } elseif (stripos('Confirm', $keyword) !== false) {
                    $query->orWhere('status', 'confirm');
                }
            })
            ->addColumn('status', function (JobOnBoard $jobOnBoards) {
                $statuses = [
                    'pending' => 'bg-warning',
                    'cancel' => 'bg-danger',
                    'confirm' => 'bg-success',
                ];

                $class = isset($statuses[$jobOnBoards->status]) ? $statuses[$jobOnBoards->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3 status-badge">' . JobOnBoard::$status[$jobOnBoards->status] . '</span>';
            })
            ->filterColumn('application', function ($query, $keyword) {
                $query->whereHas('applications', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
        if (\Laratrust::hasPermission('jobonboard ediconvertt') || \Laratrust::hasPermission('jobonboard edit') || \Laratrust::hasPermission('jobonboard delete')) {
            $dataTable->addColumn('action', function (JobOnBoard $jobOnBoards) {
                return view('recruitment::jobApplication.button-jobonboard', compact('jobOnBoards'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobOnBoard $model, Request $request): QueryBuilder
    {
        $jobOnBoards = $model->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->with(['applications', 'branches', 'UserName','applications.jobs']);

        return $jobOnBoards;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('job-on-board-table')
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
            Column::make('application')->title(__('Name')),
            Column::make('job')->title(__('Job'))->name('applications.jobs.title'),
            Column::make('type')->title(__('Type')),
            Column::computed('account')->title(__('Account')),
            Column::make('location')->title(__('Location'))->name('applications.jobs.location'),
            Column::computed('applied_at')->title(__('Applied at')),
            Column::make('joining_date')->title(__('Joining At')),
            Column::make('status')->title(__('Status')),
        ];
        if (
            \Laratrust::hasPermission('jobonboard convert') ||
            \Laratrust::hasPermission('jobonboard edit') ||
            \Laratrust::hasPermission('jobonboard delete')
        ) {
            $action = [
                Column::computed('action')
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
        return 'Job On-Boards_' . date('YmdHis');
    }
}
