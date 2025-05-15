<?php

namespace Workdo\Training\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Workdo\Training\Entities\Training;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TrainingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['branch', 'training_type', 'status', 'employee', 'trainer', 'start_date', 'training_cost'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('branch', function (Training $trainings) {
                return $trainings->branch ? $trainings->branches->name ?? '-' : '-';
            })
            ->editColumn('training_type', function (Training $trainings) {
                return $trainings->training_type ? $trainings->types->name ?? '-' : '-';
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Pending', $keyword) !== false) {
                    $query->where('status', 0);
                } elseif (stripos('Started', $keyword) !== false) {
                    $query->orWhere('status', 1);
                } elseif (stripos('Completed', $keyword) !== false) {
                    $query->orWhere('status', 2);
                } elseif (stripos('Terminated', $keyword) !== false) {
                    $query->orWhere('status', 3);
                }
            })
            ->addColumn('status', function ($trainings) {
                $statuses = [
                    0 => 'bg-warning',
                    1 => 'bg-primary',
                    2 => 'bg-success',
                    3 => 'bg-danger',
                ];

                $class = isset($statuses[$trainings->status]) ? $statuses[$trainings->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3 mt-1 status-badge6">' . Training::$Status[$trainings->status] . '</span>';
            })
            ->editColumn('employee', function (Training $trainings) {
                return $trainings->employee ? $trainings->employees->name ?? '-' : '-';
            })
            ->editColumn('trainer', function (Training $trainings) {
                return $trainings->trainer ? $trainings->trainers->firstname . ' ' . $trainings->trainers->lastname ?? '-' : '-';
            })
            ->editColumn('start_date', function (Training $trainings) {
                return $trainings->start_date && $trainings->end_date ? company_date_formate($trainings->start_date) . ' to ' . company_date_formate($trainings->end_date) ?? '-' : '-';
            })
            ->editColumn('training_cost', function (Training $trainings) {
                return $trainings->training_cost ? currency_format_with_sym($trainings->training_cost) ?? '-' : '-';
            })
            ->filterColumn('branch', function ($query, $keyword) {
                $query->whereHas('branches', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('training_type', function ($query, $keyword) {
                $query->whereHas('types', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('employee', function ($query, $keyword) {
                $query->whereHas('employees', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('trainer', function ($query, $keyword) {
                $query->whereHas('trainers', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%$keyword%");
                });
            });
        if (\Laratrust::hasPermission('training show') || \Laratrust::hasPermission('training edit') || \Laratrust::hasPermission('training delete')) {
            $dataTable->addColumn('action', function (Training $trainings) {
                return view('training::training.button', compact('trainings'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Training $model, Request $request): QueryBuilder
    {
        $trainings = $model->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->with(['branches', 'types', 'employees', 'trainers']);

        return $trainings;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('training-table')
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
        $company_settings = getCompanyAllSetting();
        $column = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('branch')->title(!empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch')),
            Column::make('training_type')->title(__('Training Type')),
            Column::make('status')->title(__('Status')),
            Column::make('employee')->title(__('Employee')),
            Column::make('trainer')->title(__('Trainer')),
            Column::make('start_date')->title(__('Training Duration')),
            Column::make('training_cost')->title(__('Cost')),
        ];
        if (
            \Laratrust::hasPermission('training show') ||
            \Laratrust::hasPermission('training edit') ||
            \Laratrust::hasPermission('training delete')
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
        return 'Trainings_' . date('YmdHis');
    }
}
