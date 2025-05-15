<?php

namespace Workdo\Contract\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Contract\Entities\Contract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ContractDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['contract_id','user','project','value','start_date','end_date','status'];
        $dataTable = (new EloquentDataTable($query))
            ->editColumn('contract_id', function (Contract $contract) {
            if (\Laratrust::hasPermission('contract show'))
            {
                $html = '<a href="'.route('contract.show',$contract->id).'" class="btn btn-outline-primary">'. Contract::contractNumberFormat($contract->contract_id) .'</a>';
            }
            else
            {
                $html = '<a class="btn btn-outline-primary">'. Contract::contractNumberFormat($contract->contract_id) .'</a>';

            }
            return $html;
            })
            ->editColumn('user', function (Contract $contract) {
                return $contract->user_name;
            })
            ->editColumn('project', function (Contract $contract) {
                return $contract->project_name;
            })
            ->editColumn('value', function (Contract $contract) {
                return currency_format_with_sym($contract->value);
            })
            ->editColumn('start_date', function (Contract $contract) {
                return company_date_formate($contract->start_date);
            })
            ->editColumn('end_date', function (Contract $contract) {
                return company_date_formate($contract->end_date);
            })
            ->editColumn('status', function (Contract $contract) {
                $html='';
                if ($contract->status == "pending") {
                    $html = '<span class="badge bg-warning p-2 px-3">' . __('Pending') . '</span>';
                } elseif ($contract->status == "accept") {
                    $html = '<span class="badge bg-primary p-2 px-3">' . __('Accept') . '</span>';
                }
                return $html;
            });
            if (\Laratrust::hasPermission('contract delete') || \Laratrust::hasPermission('contract edit')  || \Laratrust::hasPermission('contract show'))
            {
                $dataTable->addColumn('action', function (Contract $contract) {
                    return view('contract::contracts.action', compact('contract'));
                });
                $rowColumn[] = 'action';
            }
            return $dataTable->rawColumns($rowColumn);


    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Contract $model): QueryBuilder
    {
        if(Auth::user()->type == 'company')
        {
            $contracts = Contract::select('contracts.*', 'contract_types.name as type','users.name as user_name','projects.name as project_name')
            ->leftJoin('contract_types', 'contracts.type', '=', 'contract_types.id')->leftJoin('users', 'contracts.user_id', '=', 'users.id')
            ->leftJoin('projects', 'contracts.project_id', '=', 'projects.id')
            ->contractonly()->where('contracts.created_by', '=', creatorId())->where('contracts.workspace', getActiveWorkSpace());
        }
        else
        {

            $contracts   = Contract::select('contracts.*', 'contract_types.name as type','users.name as user_name','projects.name as project_name')
                    ->leftJoin('contract_types', 'contracts.type', '=', 'contract_types.id')->leftJoin('users', 'contracts.user_id', '=', 'users.id')
                    ->leftJoin('projects', 'contracts.project_id', '=', 'projects.id')->where('user_id', '=', Auth::user()->id);
        }
        return $contracts;

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('contract-table')
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
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('contract_id')->title(__('Contract')),
            Column::make('subject')->title(__('Subject')),
            Column::make('user')->title(__('User'))->name('users.name'),
            Column::make('project')->title(__('Project'))->name('projects.name'),
            Column::make('value')->title(__('Value')),
            Column::make('type')->title(__('Type'))->name('contract_types.name'),
            Column::make('start_date')->title(__('Start Date')),
            Column::make('end_date')->title(__('End Date')),
            Column::make('status')->title(__('Status')),

        ];
        if (\Laratrust::hasPermission('contract delete') ||
            \Laratrust::hasPermission('contract edit') ||
            \Laratrust::hasPermission('contract show'))
         {
            $action = [
                Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                
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
        return 'Contracts_' . date('YmdHis');
    }
}
