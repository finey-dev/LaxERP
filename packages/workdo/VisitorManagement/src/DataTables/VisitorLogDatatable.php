<?php

namespace Workdo\VisitorManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Workdo\VisitorManagement\Entities\Visitlog;
use Workdo\VisitorManagement\Entities\Visitors;
use Carbon\Carbon;

class VisitorLogDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    // Assuming this is within your DataTable class

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['f_name', 'l_name', 'check_out', 'duration_of_visit'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('f_name', function (VisitLog $visitLog) {
                return !empty($visitLog->visitor_id) ? $visitLog->visitor->first_name : '';
            })
            ->editColumn('l_name', function (VisitLog $visitLog) {
                return !empty($visitLog->visitor_id) ? $visitLog->visitor->last_name : '';
            })
            ->editColumn('check_out', function (VisitLog $visitLog) {
                return !empty($visitLog->check_out) ? company_datetime_formate($visitLog->check_out) : '-' ;
            })
            ->filterColumn('check_out', function ($query, $keyword) {
                try {
                    if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                        // Full date 'd-m-Y'
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                        return $query->where('check_out', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                        // Month-Year 'm-Y'
                        $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                        return $query->where('check_out', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                        // Day-Month 'd-m'
                        $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                        return $query->where('check_out', 'LIKE', "%$date%");
                    } else {
                        // Separate day, month, and year checks
                        $hasDay = false;
                        $hasMonth = false;
                        $hasYear = false;

                        if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                            $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                            $query->whereRaw('DAY(check_out) = ?', [$day]);
                            $hasDay = true;
                        }
                        if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                            $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                            $query->orWhereRaw('MONTH(check_out) = ?', [$month]);
                            $hasMonth = true;
                        }
                        if (preg_match('/^\d{4}$/', $keyword)) {
                            $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                            $query->orWhereRaw('YEAR(check_out) = ?', [$year]);
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
            ->editColumn('duration_of_visit', function (VisitLog $visitLog) {
                return $visitLog->duration_of_visit ?? '-';
            })
            ->editColumn('check_in', function (VisitLog $visitLog) {
                return company_datetime_formate($visitLog->check_in);
            })
            ->filterColumn('check_in', function ($query, $keyword) {
                try {
                    if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                        // Full date 'd-m-Y'
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                        return $query->where('check_in', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                        // Month-Year 'm-Y'
                        $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                        return $query->where('check_in', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                        // Day-Month 'd-m'
                        $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                        return $query->where('check_in', 'LIKE', "%$date%");
                    } else {
                        // Separate day, month, and year checks
                        $hasDay = false;
                        $hasMonth = false;
                        $hasYear = false;

                        if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                            $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                            $query->whereRaw('DAY(check_in) = ?', [$day]);
                            $hasDay = true;
                        }
                        if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                            $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                            $query->orWhereRaw('MONTH(check_in) = ?', [$month]);
                            $hasMonth = true;
                        }
                        if (preg_match('/^\d{4}$/', $keyword)) {
                            $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                            $query->orWhereRaw('YEAR(check_in) = ?', [$year]);
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
            });

        if (\Laratrust::hasPermission('visitor log delete') || \Laratrust::hasPermission('visitor log edit')) {
            $dataTable->addColumn('action', function (VisitLog $visitLog) {
                return view('visitor-management::visitor_log_action', compact('visitLog'));
            });
            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);
    }



    public function query(): QueryBuilder
    {
        return VisitLog::query()
            ->select('visit_logs.*', 'visitors.first_name as f_name', 'visitors.last_name as l_name')
            ->join('visitors', 'visitors.id', '=', 'visit_logs.visitor_id')
            ->where('visit_logs.created_by', creatorId())
            ->where('visit_logs.workspace', getActiveWorkSpace());
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('visit_logs-table')
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

                $("body").on("click", "#applyfilter", function() {
                    if (!$("select[name=item_type]").val() && !$("select[name=category]").val()) {
                        toastrs("Error!", "Please select At least One Filter", "error");
                        return;
                    }
                    $("#visitor-log-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=item_type]").val("");
                    $("select[name=category]").val("");
                    $("#visitor-log-table").DataTable().draw();
                });

                var searchInput = $(\'#\' + table.api().table().container().id + \' label input[type="search"]\');
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
            <'dataTable-top'<'dataTable-dropdown page-dropdown'l><'dataTable-botton table-btn dataTable-search tb-search d-flex justify-content-end gap-2'Bf>>
            <'dataTable-container'<'col-sm-12'tr>>
            <'dataTable-bottom row'<'col-5'i><'col-7'p>>",
            'buttons' => $buttonsConfig,
            "drawCallback" => 'function(settings) {
                var tooltipTriggerList = [].slice.call(
                    document.querySelectorAll("[data-bs-toggle=tooltip]")
                );
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                var popoverTriggerList = [].slice.call(
                    document.querySelectorAll("[data-bs-toggle=popover]")
                );
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
                var toastElList = [].slice.call(document.querySelectorAll(".toast"));
                var toastList = toastElList.map(function(toastEl) {
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
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false)->orderBy(0),
            Column::make('id')->title(__('ID'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('f_name')->title(__('First Name'))->name('visitors.first_name'),
            Column::make('l_name')->title(__('Last Name'))->name('visitors.last_name'),
            Column::make('check_in')->title(__('Arrival')),
            Column::make('check_out')->title(__('Departure')),
            Column::make('duration_of_visit')->title(__('Duration')),
        ];
        if (\Laratrust::hasPermission('visitor log delete') || \Laratrust::hasPermission('visitor log edit')) {
            $action = [
                Column::computed('action')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->title(__('Action')),
            ];
            $column  = array_merge($column, $action);
        }
        return $column;
    }

    protected function filename(): string
    {
        return 'visit_logs_' . date('YmdHis');
    }
}
