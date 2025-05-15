<?php

namespace Workdo\Performance\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Performance\Entities\Indicator;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class IndicatorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['branch', 'department', 'designation', 'rating', 'created_by', 'created_at'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('branch', function (Indicator $indicators) {
                return !empty($indicators->branch) ? $indicators->branches->name ?? '-' : '-';
            })
            ->editColumn('department', function (Indicator $indicators) {
                return !empty($indicators->department) ? $indicators->departments->name ?? '-' : '-';
            })
            ->editColumn('designation', function (Indicator $indicators) {
                return !empty($indicators->designation) ? $indicators->designations->name ?? '-' : '-';
            })
            ->editColumn('rating', function (Indicator $indicator) {
                if (!empty($indicator->rating)) {
                    $rating = json_decode($indicator->rating, true);
                    if (!empty($rating)) {
                        $starsum = array_sum($rating);
                        $overallrating = $starsum / count($rating);
                    } else {
                        $overallrating = 0;
                    }
                } else {
                    $overallrating = 0;
                }

                $starsHtml = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($overallrating < $i) {
                        if (is_float($overallrating) && round($overallrating) == $i) {
                            $starsHtml .= '<i class="text-warning fas fa-star-half-alt"></i>';
                        } else {
                            $starsHtml .= '<i class="fas fa-star"></i>';
                        }
                    } else {
                        $starsHtml .= '<i class="text-warning fas fa-star"></i>';
                    }
                }

                return $starsHtml . '<span class="theme-text-color">(' . number_format($overallrating, 1) . ')</span>';
            })
            ->editColumn('created_by', function (Indicator $indicators) {
                return !empty($indicators->created_by) ? $indicators->user->name ?? '-' : '-';
            })
            ->editColumn('created_at', function (Indicator $indicators) {
                return !empty($indicators->created_at) ? company_date_formate($indicators->created_at) ?? '-' : '-';
            })
            ->filterColumn('branch', function ($query, $keyword) {
                $query->whereHas('branches', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('departments', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('designation', function ($query, $keyword) {
                $query->whereHas('designations', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
        if (\Laratrust::hasPermission('indicator show') || \Laratrust::hasPermission('indicator edit') || \Laratrust::hasPermission('indicator delete')) {
            $dataTable->addColumn('action', function (Indicator $indicators) {
                return view('performance::indicator.button', compact('indicators'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Indicator $model, Request $request): QueryBuilder
    {
        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $employee = Employee::where('user_id', Auth::user()->id)->first();
            $indicators = [];
            if (!empty($employee)) {
                $indicators = $model->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('branch', $employee->branch_id)->where('department', $employee->department_id)->where('designation', $employee->designation_id);
            }
        } else {
            $indicators = $model->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->with(['user', 'branches', 'departments', 'designations']);
        }

        return $indicators;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('indicator-table')
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
            Column::make('department')->title(!empty($company_settings['hrm_department_name']) ? $company_settings['hrm_department_name'] : __('Department')),
            Column::make('designation')->title(!empty($company_settings['hrm_designation_name']) ? $company_settings['hrm_designation_name'] : __('Designation')),
            Column::make('rating')->title(__('Overall Rating'))->printable(false),
            Column::make('created_by')->title(__('Added By')),
            Column::make('created_at')->title(__('Created At')),
        ];
        if (
            \Laratrust::hasPermission('indicator show') ||
            \Laratrust::hasPermission('indicator edit') ||
            \Laratrust::hasPermission('indicator delete')
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
        return 'Indicators_' . date('YmdHis');
    }
}
