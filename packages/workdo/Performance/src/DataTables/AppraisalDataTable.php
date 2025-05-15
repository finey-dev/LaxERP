<?php

namespace Workdo\Performance\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Performance\Entities\Appraisal;
use Workdo\Performance\Entities\Competencies;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AppraisalDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['branch', 'department', 'designation', 'employee', 'target_rating', 'rating', 'appraisal_date'];
        $dataTable = (new EloquentDataTable($query->with(['branches', 'employees.department', 'employees.designation'])))
            ->addIndexColumn()
            ->editColumn('branch', function (Appraisal $appraisals) {
                return !empty($appraisals->branch) ? $appraisals->branches->name ?? '-' : '-';
            })
            ->editColumn('department', function (Appraisal $appraisals) {
                return !empty($appraisals->employees) && isset($appraisals->employees->department) ? $appraisals->employees->department->name ?? '-' : '-';
            })
            ->editColumn('designation', function (Appraisal $appraisals) {
                return !empty($appraisals->employees) && isset($appraisals->employees->designation) ? $appraisals->employees->designation->name ?? '-' : '-';
            })
            ->editColumn('employee', function (Appraisal $appraisals) {
                return !empty($appraisals->employees) ? $appraisals->employees->name ?? '-' : '-';
            })
            ->addColumn('target_rating', function (Appraisal $appraisals) {
                $designation = !empty($appraisals->employees) ? $appraisals->employees->designation->id : '-';
                $competencyCount = Competencies::where('created_by', '=', $appraisals->created_by)->where('workspace', getActiveWorkSpace())->count();
                $targetRating = Appraisal::getTargetrating($designation, $competencyCount);

                $html = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($targetRating < $i) {
                        if (is_float($targetRating) && round($targetRating) == $i) {
                            $html .= '<i class="text-warning fas fa-star-half-alt"></i>';
                        } else {
                            $html .= '<i class="fas fa-star"></i>';
                        }
                    } else {
                        $html .= '<i class="text-warning fas fa-star"></i>';
                    }
                }
                $html .= '<span class="theme-text-color">(' . number_format($targetRating, 1) . ')</span>';

                return $html;
            })
            ->editColumn('rating', function (Appraisal $appraisals) {
                $designation = !empty($appraisals->employees) ? $appraisals->employees->designation->id : '-';
                $competencyCount = Competencies::where('created_by', '=', $appraisals->created_by)->where('workspace', getActiveWorkSpace())->count();
                $targetRating = Appraisal::getTargetrating($designation, $competencyCount);

                if (!empty($appraisals->rating) && ($competencyCount != 0)) {
                    $rating = json_decode($appraisals->rating, true);
                    $starsum = array_sum($rating);
                    $overallrating = $starsum / $competencyCount;
                } else {
                    $overallrating = 0;
                }

                $html = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($overallrating < $i) {
                        if (is_float($overallrating) && round($overallrating) == $i) {
                            $html .= '<i class="text-warning fas fa-star-half-alt"></i>';
                        } else {
                            $html .= '<i class="fas fa-star"></i>';
                        }
                    } else {
                        $html .= '<i class="text-warning fas fa-star"></i>';
                    }
                }
                $html .= '<span class="theme-text-color">(' . number_format($overallrating, 1) . ')</span>';

                return $html;
            })
            ->editColumn('appraisal_date', function (Appraisal $appraisals) {
                return $appraisals->appraisal_date ?? '-';
            })
            ->filterColumn('branch', function ($query, $keyword) {
                $query->whereHas('branches', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('employees.department', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('designation', function ($query, $keyword) {
                $query->whereHas('employees.designation', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('employee', function ($query, $keyword) {
                $query->whereHas('employees', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });

        if (\Laratrust::hasPermission('appraisal show') || \Laratrust::hasPermission('appraisal edit') || \Laratrust::hasPermission('appraisal delete')) {
            $dataTable->addColumn('action', function (Appraisal $appraisals) {
                return view('performance::appraisal.button', compact('appraisals'));
            });
            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Appraisal $model, Request $request): QueryBuilder
    {
        $user = Auth::user();
        $competencyCount = 0;

        if (!in_array($user->type, $user->not_emp_type)) {
            $employee = Employee::where('user_id', $user->id)->first();

            if (!empty($employee)) {
                $competencyCount = Competencies::where('created_by', '=', $employee->created_by)
                    ->where('workspace', getActiveWorkSpace())
                    ->count();
                $query = $model->where('appraisals.created_by', '=', creatorId())
                    ->where('appraisals.workspace', getActiveWorkSpace())
                    ->where('branch', $employee->branch_id)
                    ->where('employee', $employee->id)
                    ->with('employees', 'branches', 'employees.department', 'employees.designation');
            } else {
                $query = $model->newQuery();
            }
        } else {
            $competencyCount = Competencies::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->count();
            $query = $model->where('appraisals.created_by', '=', creatorId())
                ->where('appraisals.workspace', getActiveWorkSpace())
                ->with(['employees', 'branches', 'employees.department', 'employees.designation']);
        }

        $this->competencyCount = $competencyCount;

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('appraisal-table')
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
            Column::make('branch')->title(!empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch')),
            Column::make('department')->title(!empty($company_settings['hrm_department_name']) ? $company_settings['hrm_department_name'] : __('Department'))->name('employees.department.name'),
            Column::make('designation')->title(!empty($company_settings['hrm_designation_name']) ? $company_settings['hrm_designation_name'] : __('Designation'))->name('employees.designation.name'),
            Column::computed('target_rating')->title(__('Target Rating'))->printable(false),
            Column::make('rating')->title(__('Overall Rating'))->printable(false),
            Column::make('appraisal_date')->title(__('Appraisal Date')),
        ];
        if (in_array(\Auth::user()->type, \Auth::user()->not_emp_type)) {
            $employee = [
                Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
                Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
                Column::make('employee')->title(__('Employee')),
            ];
            $column = array_merge($employee, $column);
        } else {
            $employee = [
                Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
                Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            ];
            $column = array_merge($employee, $column);
        }
        if (
            \Laratrust::hasPermission('appraisal show') ||
            \Laratrust::hasPermission('appraisal edit') ||
            \Laratrust::hasPermission('appraisal delete')
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
        return 'Appraisals_' . date('YmdHis');
    }
}
