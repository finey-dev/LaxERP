<?php

namespace Workdo\Sales\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Entities\Meeting;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesMeetingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name', 'parent', 'status','start_date','user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (Meeting $meeting) {
                $html = '';
                if (\Laratrust::hasPermission('meeting show')) {
                    $html.= '<a data-url="'. route("meeting.show", $meeting->id) .'" href="#" data-ajax-popup="true" data-size="md" data-title="'. __("Meeting Details") .'" class="action-item text-primary">';
                    $html.= ucfirst($meeting->name);
                    $html.= '</a>';
                } else {
                    $html.= '<a href="#" data-size="md" data-title="'. __("Meeting Details") .'" class="action-item text-primary">';
                    $html.= ucfirst($meeting->name);
                    $html.= '</a>';
                }
                return $html;
            })
            ->filterColumn('parent', function ($query, $keyword) {
                if (stripos('Account', $keyword) !== false) {
                    $query->where('parent', 'account');
                }
                elseif (stripos('Contact', $keyword) !== false) {
                    $query->orWhere('parent', 'contact');
                }
                elseif (stripos('Opportunities', $keyword) !== false) {
                    $query->orWhere('parent', 'opportunities');
                }
                elseif (stripos('Case', $keyword) !== false) {
                    $query->orWhere('parent', 'case');
                }
            })
            ->editColumn('parent', function (Meeting $meeting) {
                return Meeting::$parent[$meeting->parent];
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Planned', $keyword) !== false) {
                    $query->where('status', 0);
                }
                elseif (stripos('Held', $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
                elseif (stripos('Not Held', $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
            })
            ->editColumn('status', function (Meeting $meeting) {
                $status = [
                    0 => 'bg-success',
                    1 => 'bg-warning',
                    2 => 'bg-danger',
                ];

                $class = isset($status[$meeting->status]) ? $status[$meeting->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3" style="width: 73px;">' . Meeting::$status[$meeting->status] . '</span>';
            })
            ->editColumn('start_date', function (Meeting $meeting) {
                return company_date_formate($meeting->start_date);
            })
            ->editColumn('user_id', function (Meeting $meeting) {
                return ucfirst(optional($meeting->assign_user)->name ?? '-');
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('assign_user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('meeting show') ||
                \Laratrust::hasPermission('meeting edit') ||
                \Laratrust::hasPermission('meeting delete')) {
                $dataTable->addColumn('action', function (Meeting $meeting) {
                    return view('sales::meeting.meeting_action', compact('meeting'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Meeting $model, Request $request)
    {
        return $model->where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales-meeting-table')
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
            Column::make('parent')->title(__('Parent')),
            Column::make('status')->title(__('Status')),
            Column::make('start_date')->title(__('Date Start')),
            Column::make('user_id')->title(__('Assigned User')),
        ];

        if (\Laratrust::hasPermission('meeting show') ||
            \Laratrust::hasPermission('meeting edit') ||
            \Laratrust::hasPermission('meeting delete')) {
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
        return 'SalesMeeting_' . date('YmdHis');
    }
}
