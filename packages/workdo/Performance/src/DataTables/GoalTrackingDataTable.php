<?php

namespace Workdo\Performance\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Performance\Entities\GoalTracking;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class GoalTrackingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['goal_type', 'subject', 'branch', 'target_achievement', 'start_date', 'end_date', 'rating', 'progress'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('goal_type', function (GoalTracking $goalTrackings) {
                return !empty($goalTrackings->goal_type) ? $goalTrackings->goalType->name ?? '-' : '-';
            })
            ->editColumn('subject', function (GoalTracking $goalTrackings) {
                return $goalTrackings->subject ?? '-';
            })
            ->editColumn('branch', function (GoalTracking $goalTrackings) {
                return !empty($goalTrackings->branch) ? $goalTrackings->branches->name ?? '-' : '-';
            })
            ->editColumn('target_achievement', function (GoalTracking $goalTrackings) {
                return $goalTrackings->target_achievement ?? '-';
            })
            ->editColumn('start_date', function (GoalTracking $goalTrackings) {
                return !empty($goalTrackings->start_date) ? company_date_formate($goalTrackings->start_date) ?? '-' : '-';
            })
            ->editColumn('end_date', function (GoalTracking $goalTrackings) {
                return !empty($goalTrackings->end_date) ? company_date_formate($goalTrackings->end_date) ?? '-' : '-';
            })
            ->editColumn('rating', function (GoalTracking $goalTrackings) {
                $html = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($goalTrackings->rating < $i) {
                        $html .= '<i class="fas fa-star"></i>';
                    } else {
                        $html .= '<i class="text-warning fas fa-star"></i>';
                    }
                }
                return $html;
            })
            ->editColumn('progress', function (GoalTracking $goalTrackings) {
                $progressHtml = '
                    <div class="progress-wrapper">
                        <span class="progress-percentage"><small class="font-weight-bold"></small>' . $goalTrackings->progress . '%</span>
                        <div class="progress progress-xs mt-2 w-100">
                            <div class="progress-bar bg-' . GoalTracking::getProgressColor($goalTrackings->progress) . '"
                                role="progressbar" aria-valuenow="' . $goalTrackings->progress . '"
                                aria-valuemin="0" aria-valuemax="100"
                                style="width: ' . $goalTrackings->progress . '%;"></div>
                        </div>
                    </div>';
                return $progressHtml;
            })
            ->filterColumn('goal_type', function ($query, $keyword) {
                $query->whereHas('goalType', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('branch', function ($query, $keyword) {
                $query->whereHas('branches', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('goaltracking edit') || \Laratrust::hasPermission('goaltracking delete')) {
                $dataTable->addColumn('action', function (GoalTracking $goalTrackings) {
                    return view('performance::goaltracking.button', compact('goalTrackings'));
                });
                $rowColumn[] = 'action';
            }
            return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(GoalTracking $model, Request $request): QueryBuilder
    {
        $user = Auth::user();
        if (!in_array($user->type, $user->not_emp_type)) {
            $employee      = Employee::where('user_id', $user->id)->first();
            $goalTrackings = [];
            if (!empty($employee)) {
                $goalTrackings = GoalTracking::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('branch', $employee->branch_id)->with('goalType');
            }
        } else {
            $goalTrackings = GoalTracking::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->with(['goalType', 'branches']);
        }

        return $goalTrackings;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('goal-tracking-table')
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
            Column::make('goal_type')->title(__('Goal Type')),
            Column::make('subject')->title(__('Subject')),
            Column::make('branch')->title(!empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch')),
            Column::make('target_achievement')->title(__('Target Achievement')),
            Column::make('start_date')->title(__('Start Date')),
            Column::make('end_date')->title(__('End Date')),
            Column::make('rating')->title(__('Rating'))->printable(false)->exportable(false),
            Column::make('progress')->title(__('Progress'))->addClass('progress-column'),
        ];
        if (
            \Laratrust::hasPermission('goaltracking edit') ||
            \Laratrust::hasPermission('goaltracking delete')
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
        return 'Goal Tracking_' . date('YmdHis');
    }
}
