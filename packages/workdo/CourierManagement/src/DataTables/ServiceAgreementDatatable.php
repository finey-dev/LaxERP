<?php

namespace Workdo\CourierManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Workdo\CourierManagement\Entities\ServiceAgreements;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServiceAgreementDatatable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumn = [];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('start_date', function (ServiceAgreements $agreement_details) {
                return company_date_formate($agreement_details->start_date);
            })
            ->editColumn('end_date', function (ServiceAgreements $agreement_details) {
                return company_date_formate($agreement_details->end_date);
            })
            ->filterColumn('start_date', function ($query, $keyword) {
                try {
                    if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                        return $query->where('start_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                        $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                        return $query->where('start_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                        $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                        return $query->where('start_date', 'LIKE', "%$date%");
                    } else {
                        $hasDay = false;
                        $hasMonth = false;
                        $hasYear = false;
                        if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                            $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                            $query->whereRaw('DAY(start_date) = ?', [$day]);
                            $hasDay = true;
                        }
                        if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                            $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                            $query->orWhereRaw('MONTH(start_date) = ?', [$month]);
                            $hasMonth = true;
                        }
                        if (preg_match('/^\d{4}$/', $keyword)) {
                            $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                            $query->orWhereRaw('YEAR(start_date) = ?', [$year]);
                            $hasYear = true;
                        }

                        if ($hasDay || $hasMonth || $hasYear) {
                            return $query;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Invalid date format: ' . $keyword);
                }
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                try {
                    if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                        $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                        $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                        return $query->where('end_date', 'LIKE', "%$date%");
                    } else {
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

                        if ($hasDay || $hasMonth || $hasYear) {
                            return $query;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Invalid date format: ' . $keyword);
                }
            });

        if (\Laratrust::hasPermission('service agreements show') || \Laratrust::hasPermission('service agreements edit') || \Laratrust::hasPermission('service agreements delete')) {
            $dataTable->addColumn('action', function (ServiceAgreements $service_agreements) {
                return view('courier-management::service-agreements.action', compact('service_agreements'));
            });
            $rawColumn[] = 'action';
        }
        return  $dataTable->rawColumns($rawColumn);
    }
    public function query(ServiceAgreements $model): QueryBuilder
    {
        return $model->select('service_agreements.*')
            ->where('workspace', getActiveWorkSpace())
            ->where('created_by', creatorId());
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('service_agreements-table')
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
            'className' => 'btn btn-light-secondary me-1 dropdown-toggle',
            'text' => '<i class="ti ti-download me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export"></i>',

            'buttons' => [
                [
                    'extend' => 'print',
                    'text' => '<i class="fas fa-print"></i> ' . __('Print'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'csv',
                    'text' => '<i class="fas fa-file-csv"></i> ' . __('CSV'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'excel',
                    'text' => '<i class="fas fa-file-excel"></i> ' . __('Excel'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
            ],
        ];

        $buttonsConfig = array_merge([
            $exportButtonConfig,
            [
                'extend' => 'reset',
                'className' => 'btn btn-light-danger me-1',
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

    public function getColumns(): array
    {
        $column = [
            Column::make('id')->visible(false)->searchable(false)->printable(false)->exportable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('customer_name')->title(__('Customer Name')),
            Column::make('start_date')->title(__('Start Date')),
            Column::make('end_date')->title(__('End Date')),

        ];
        if (\Laratrust::hasPermission('service agreements show') || \Laratrust::hasPermission('service agreements edit') || \Laratrust::hasPermission('service agreements delete')) {
            $action = [
                Column::computed('action')
                    ->title(__('Action'))
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)

            ];
        }
        $column = array_merge($column, $action);

        return $column;
    }

    protected function filename(): string
    {
        return 'ServiceAgreements_' . date('YmdHis');
    }
}
