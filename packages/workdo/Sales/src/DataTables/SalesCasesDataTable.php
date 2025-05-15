<?php

namespace Workdo\Sales\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Entities\CommonCase;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesCasesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name','attachments', 'account', 'status','priority','user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (CommonCase $case) {
                $html = '';
                if (\Laratrust::hasPermission('case edit')) {
                    $html.= '<a href="'. route("commoncases.edit", $case->id) .'" data-size="md" data-title="'. __("Cases Details") .'" class="text-primary">';
                    $html.= ucfirst($case->name);
                    $html.= '</a>';
                } else {
                    $html.= '<a href="#" data-size="md" data-title="'. __("Cases Details") .'" class="text-primary">';
                    $html.= ucfirst($case->name);
                    $html.= '</a>';
                }
                return $html;
            })
            ->editColumn('attachments', function (CommonCase $case) {
                $html = '';
                if (!empty($case->attachments)) {
                    $html.= '<a href="'. get_file($case->attachments) .'" download=""><i class="ti ti-download"></i></a>';
                } else {
                    $html.= '<span>'. __("No File") .'</span>';
                }
                return $html;
            })
            ->editColumn('account', function (CommonCase $case) {
                return ucfirst(optional($case->accounts)->name ?? '-');
            })
            ->filterColumn('account', function ($query, $keyword) {
                $query->whereHas('accounts', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('New', $keyword) !== false) {
                    $query->where('status', 0);
                }
                elseif (stripos('Assigned', $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
                elseif (stripos('Pending', $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
                elseif (stripos('Closed', $keyword) !== false) {
                    $query->orWhere('status', 3);
                }
                elseif (stripos('Rejected', $keyword) !== false) {
                    $query->orWhere('status', 4);
                }
                elseif (stripos('Duplicate', $keyword) !== false) {
                    $query->orWhere('status', 5);
                }
            })
            ->editColumn('status', function (CommonCase $case) {
                $status = [
                    0 => 'bg-success',
                    1 => 'bg-info',
                    2 => 'bg-warning',
                    3 => 'bg-danger',
                    4 => 'bg-danger',
                    5 => 'bg-warning',
                ];

                $class = isset($status[$case->status]) ? $status[$case->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3" style="width: 73px;">' . CommonCase::$status[$case->status] . '</span>';
            })
            ->filterColumn('priority', function ($query, $keyword) {
                if (stripos('Low', $keyword) !== false) {
                    $query->where('priority', 0);
                }
                elseif (stripos('Normal', $keyword) !== false) {
                    $query->orWhere('priority', 1);
                }
                elseif (stripos('High', $keyword) !== false) {
                    $query->orWhere('priority', 2);
                }
                elseif (stripos('Urgent', $keyword) !== false) {
                    $query->orWhere('priority', 3);
                }
            })
            ->editColumn('priority', function (CommonCase $case) {
                $priority = [
                    0 => 'bg-primary',
                    1 => 'bg-info',
                    2 => 'bg-warning',
                    3 => 'bg-danger',
                ];

                $class = isset($priority[$case->priority]) ? $priority[$case->priority] : '';
                return '<span class="badge ' . $class . ' p-2 px-3" style="width: 73px;">' . CommonCase::$priority[$case->priority] . '</span>';
            })
            ->editColumn('user_id', function (CommonCase $case) {
                return ucfirst(optional($case->assign_user)->name ?? '-');
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('assign_user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('case show') ||
                \Laratrust::hasPermission('case edit') ||
                \Laratrust::hasPermission('case delete')) {
                $dataTable->addColumn('action', function (CommonCase $case) {
                    return view('sales::commoncase.case_action', compact('case'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CommonCase $model)
    {
       return $model->where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales-cases-table')
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
        $columns = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('name')->title(__('Name')),
            Column::make('attachments')->title(__('File'))->searchable(false)->orderable(false)->exportable(false)->printable(false),
            Column::make('number')->title(__('Number')),
            Column::make('account')->title(__('Account')),
            Column::make('status')->title(__('Status')),
            Column::make('priority')->title(__('Priority')),
            Column::make('user_id')->title(__('Assign User')),
        ];

        if (\Laratrust::hasPermission('case show') ||
            \Laratrust::hasPermission('case edit') ||
            \Laratrust::hasPermission('case delete')) {
            $columns[] = Column::computed('action')
                ->title(__('Action'))
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ;
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Cases_' . date('YmdHis');
    }
}
