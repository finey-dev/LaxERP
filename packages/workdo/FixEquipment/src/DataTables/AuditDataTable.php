<?php

namespace Workdo\FixEquipment\DataTables;

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
use Workdo\FixEquipment\Entities\Audit;

class AuditDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['audit_status','audit_date'];
        $dataTable = (new EloquentDataTable($query))
        ->addIndexColumn()

        ->editColumn('audit_date', function (Audit $audit) {
            return company_date_formate($audit->audit_date);
        })
        ->filterColumn('audit_date', function ($query, $keyword) {
            try {
                if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                    return $query->where('audit_date', 'LIKE', "%$date%");
                } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                    $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                    return $query->where('audit_date', 'LIKE', "%$date%");
                } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                    $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                    return $query->where('audit_date', 'LIKE', "%$date%");
                } else {
                    $hasDay = false;
                    $hasMonth = false;
                    $hasYear = false;
                    if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                        $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                        $query->whereRaw('DAY(audit_date) = ?', [$day]);
                        $hasDay = true;
                    }
                    if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                        $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                        $query->orWhereRaw('MONTH(audit_date) = ?', [$month]);
                        $hasMonth = true;
                    }
                    if (preg_match('/^\d{4}$/', $keyword)) {
                        $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                        $query->orWhereRaw('YEAR(audit_date) = ?', [$year]);
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
        ->editColumn('audit_status', function (Audit $audit) {
            if ($audit->audit_status == 'Approved') {
                return '<span class="badge bg-success  p-2 px-3 text-white">Approved</span>';
            } elseif ($audit->audit_status == 'Pending') {
                return '<span class="badge bg-warning  p-2 px-3 text-white">Pending</span>';
            } else {
                return '<span class="badge bg-danger  p-2 px-3 text-white">Rejected</span>';
            }
        });

        if (\Laratrust::hasPermission('equipment audit manage') ||
            \Laratrust::hasPermission('equipment audit edit') ||
            \Laratrust::hasPermission('equipment audit delete')) {

            $dataTable->addColumn('action', function (Audit $audit) {
                return view('fix-equipment::audit.action', compact('audit'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Audit $model): QueryBuilder
    {
        $audits = $model->where('workspace', getActiveWorkSpace())->where('created_by', creatorId());

       return $audits;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('users-table')
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
        $column =  [
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('audit_title')->title(__('Title')),
            Column::make('audit_date')->title(__('Date')),
            Column::make('audit_status')->title(__('Status')),
        ];
        if (\Laratrust::hasPermission('equipment audit manage') ||
            \Laratrust::hasPermission('equipment audit edit') ||
            \Laratrust::hasPermission('equipment audit delete')) {

            $action = [
                Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)

                ->title(__('Action'))
            ];
            $column = array_merge($column,$action);
        }
        return $column;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Audits_' . date('YmdHis');
    }
}
