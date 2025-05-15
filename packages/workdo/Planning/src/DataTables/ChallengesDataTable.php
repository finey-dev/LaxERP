<?php

namespace Workdo\Planning\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Planning\Entities\PlanningChallenge;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ChallengesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['position'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('category', function (PlanningChallenge $challenge) {
                return $challenge->Categories->title ?? '---';
            })

            ->editColumn('end_date', function (PlanningChallenge $challenge) {
                return company_date_formate($challenge->end_date);
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                try {
                    if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                        // Full date 'd-m-Y'
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                        // Month-Year 'm-Y'
                        $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                        // Day-Month 'd-m'
                        $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } else {
                        // Separate day, month, and year checks
                        $hasDay = false;
                        $hasMonth = false;
                        $hasYear = false;
                        if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                            $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                            $query->whereRaw('DAY(end_date) = ?', [$day]);
                            $hasDay = true;
                        }
                        if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                            $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                            $query->orWhereRaw('MONTH(end_date) = ?', [$month]);
                            $hasMonth = true;
                        }
                        if (preg_match('/^\d{4}$/', $keyword)) {
                            $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                            $query->orWhereRaw('YEAR(end_date) = ?', [$year]);
                            $hasYear = true;
                        }
                        // Combine conditions to ensure proper logic
                        if ($hasDay || $hasMonth || $hasYear) {
                            return $query;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Invalid date format: ' . $keyword);
                }
            })
            ->editColumn('position', function (PlanningChallenge $challenge) {
                if ($challenge->position == 'Ongoing') {
                    $html = '<span class="p-2 px-3  status_badge badge bg-secondary"> ' . __("Ongoing") . '</span>';
                } elseif ($challenge->position == 'On Hold') {
                    $html = '<span class="p-2 px-3  status_badge badge bg-warning">' . __("On Hold") . '</span>';
                } else {
                    $html = '<span class="p-2 px-3  status_badge badge bg-success"> ' . __("Finished") . '  </span>';
                }
                return $html;
            })
            ->filterColumn('position', function ($query, $keyword) {
                if (stripos(__('Ongoing'), $keyword) !== false) {
                    $query->where('position', "Ongoing");
                }
                if (stripos(__('On Hold'), $keyword) !== false) {
                    $query->orwhere('position', "On Hold");
                }
                if (stripos(__('Finished'), $keyword) !== false) {
                    $query->orwhere('position', "Finished");
                }
            });

        if ((\Laratrust::hasPermission('planningchallenges show')) || (\Laratrust::hasPermission('planningchallenges edit')) || (\Laratrust::hasPermission('planningchallenges delete'))) {
            $dataTable->addColumn('action', function (PlanningChallenge $challenge) {
                return view('planning::planningchallenges.action', compact('challenge'));
            });
            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PlanningChallenge $model, Request $request): QueryBuilder
    {
        $query = $model->with('Categories')
            ->where('planning_challenges.created_by', '=', creatorId())
            ->where('planning_challenges.workspace', getActiveWorkSpace())
            ->newQuery();
        if (!empty($request->position)) {
            $query->where('position', '=', $request->position);
        }

        if (!empty($request->category)) {
            $query->where('category', '=', $request->category);
        }
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('challenges-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    d.position = $("select[name=position]").val();
                   d.category = $("select[name=category]").val();
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
                $("body").on("click", "#applyfilter", function() {

                    if (!$("select[name=position]").val() && !$("select[name=category]").val()) {
                        toastrs("Error!", "Please select Atleast One Filter ", "error");
                        return;
                    }

                    $("#challenges-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=position]").val("")
                    $("select[name=category]").val("")
                    $("#challenges-table").DataTable().draw();
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
            "dom" => "
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
            Column::make('id')->searchable(false)->printable(false)->exportable(false)->visible(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('category')->title(__('Category'))->name('Categories.title'),
            Column::make('end_date')->title(__('End Date')),
            Column::make('position')->title(__('Position')),
        ];
        if ((\Laratrust::hasPermission('planningchallenges show')) || (\Laratrust::hasPermission('planningchallenges edit')) || (\Laratrust::hasPermission('planningchallenges delete'))) {
            $action = [
                Column::computed('action')
                    ->title(__('Action'))
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
        return 'Challenges_' . date('YmdHis');
    }
}
