<?php

namespace Workdo\SalesAgent\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Entities\Program;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name', 'sales_agents_applicable'];
        $dataTable =  (new EloquentDataTable($query))->addIndexColumn()
            ->editColumn('name', function (Program $program) {
                $html = '<a href="' . route('programs.show', \Crypt::encrypt($program->id)) . '" class="">' . $program->name . '</a>';
                return $html;
            })
            ->editColumn('sales_agents_applicable', function (Program $program) {
                $users = User::whereIn('id', explode(',', $program->sales_agents_applicable))->get();
                $html = '<td class="user-group">';
                foreach ($users as $user) {
                    $avatar = $user->avatar ? get_file($user->avatar) : get_file('avatar.png');
                    $html .= '<img data-bs-toggle="tooltip" data-bs-placement="top"';
                    $html .= ' title="' . $user->name . '"';
                    $html .= ' src="' . $avatar . '"';
                    $html .= ' class="rounded-circle" width="25" height="25">';
                }
                $html .= '</td>';
                return $html;
            });

        if (\Laratrust::hasPermission(['salesagent programs show', 'programs edit', 'programs delete', 'programs show'])) {
            $rowColumn[] = 'action';
            $dataTable->addColumn('action', function (Program $action) {
                return view('sales-agent::programs.action', compact('action'));
            });
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */


    public function query(Program $model): QueryBuilder
    {
        $model->with('userApplicable');
        if (Auth::user() && Auth::user()->isAbleTo('programs show') && Auth::user()->type == 'company') {
            return $model->where('workspace', getActiveWorkSpace());
        } else {
            $userId = Auth::user()->id;
            return $model->where('workspace', getActiveWorkSpace())
                ->where(function ($query) use ($userId) {
                    $query->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [$userId])
                        ->orWhereRaw('FIND_IN_SET(?, sales_agents_view)', [$userId]);
                });
        }
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales_agents_programs-table')
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
        $columns =   [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false)->name('sales_agents_programs.id'),
            Column::make('name')->title(__('Program Name')),
            Column::make('sales_agents_applicable')->title(__('Sales Agents')),
            Column::make('from_date')->title(__('From')),
            Column::make('to_date')->title(__('To')),
        ];

        if (\Laratrust::hasPermission(['salesagent programs show', 'programs edit', 'programs delete', 'programs show'])) {
            $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60);
        }
        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Program_' . date('YmdHis');
    }
}
