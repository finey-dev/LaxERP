<?php

namespace Workdo\CourierManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Workdo\CourierManagement\Entities\CourierAgents;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CourierAgentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumn = ['status'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('branch_id', function ($courier_agent) {
                return $courier_agent->branch ? $courier_agent->branch->branch_name : '-'; // Use the branch relationship to get the name
            })
            ->editColumn('status', function (CourierAgents $courier_agent) {
                if ($courier_agent->status == 0) {
                    $html = "<span class='badge bg-danger p-2 px-3 '>".__('Inactive')."</span>";
                } else if ($courier_agent->status == 1) {
                    $html = "<span class='badge bg-primary p-2 px-3'>".__('Active')."</span>";
                }
                return $html;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('active', $keyword) !== false) {
                    return $query->where('status', 1);
                }
                if (stripos('inactive', $keyword) !== false) {
                    return $query->orWhere('status', 0);
                }
            });

        if (\Laratrust::hasPermission('courier agents show') || \Laratrust::hasPermission('courier agents edit') || \Laratrust::hasPermission('courier agents delete')) {
            $dataTable->addColumn('action', function (CourierAgents $courier_agent) {
                return view('courier-management::courier-agents.action', compact('courier_agent'));
            });
            $rawColumn[] = 'action';
        }
        return  $dataTable->rawColumns($rawColumn);
    }
    public function query(CourierAgents $model): QueryBuilder
    {
        return $model->with('branch') //  load the branch relationship
            ->where('workspace', getActiveWorkSpace())
            ->where('created_by', creatorId());
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('courier_agents-table')
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

    public function getColumns(): array
    {
        $column = [
            Column::make('id')->visible(false)->searchable(false)->printable(false)->exportable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__(' Name')),
            Column::make('phone')->title(__(' Phone')),
            Column::make('email')->title(__(' E-mail')),
            Column::make('address')->title(__('Address')),
            Column::make('branch_id')->title(__('Branch'))->name('branch.branch_name'),
            Column::make('status')->title(__('Status')),
        ];
        if (\Laratrust::hasPermission('courier agents show') || \Laratrust::hasPermission('courier agents edit') || \Laratrust::hasPermission('courier agents delete')) {
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
        return 'CourierAgents_' . date('YmdHis');
    }
}
