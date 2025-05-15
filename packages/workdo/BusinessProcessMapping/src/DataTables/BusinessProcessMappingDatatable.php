<?php

namespace Workdo\BusinessProcessMapping\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\BusinessProcessMapping\Entities\BusinessProcessMapping;
use Workdo\BusinessProcessMapping\Entities\Related;
use Workdo\Contract\Entities\Contract;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\Lead;
use Modules\PropertyManagement\Entities\Property;
use Workdo\PropertyManagement\Entities\Property as EntitiesProperty;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\Task;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BusinessProcessMappingDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['related_to', 'related_assign', 'description'];
        $dataTable =  (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('related_to', function (BusinessProcessMapping $businessProcessMapping) {

                $relatedToLabels = [
                    1 => __('Project'),
                    2 => __('Task'),
                    3 => __('Lead'),
                    4 => __('Deal'),
                    5 => __('Property'),
                    6 => __('Contract'),
                ];

                $label = isset($relatedToLabels[$businessProcessMapping->related_to]) ? $relatedToLabels[$businessProcessMapping->related_to] : __('Other');

                return '<span class="badge p-2 m-1 px-3 bg-danger">' . $label . '</span>';
            })->editColumn('description', function (BusinessProcessMapping $businessProcessMapping) {
                $url = route('business-process-mapping.show', $businessProcessMapping->id);
                $html = '<a class="action-item" data-url="' . $url . '" data-ajax-popup="true" data-bs-toggle="tooltip" title="' . __('Description') . '" data-title="' . __('Description') . '"><i class="fa fa-comment"></i></a>';
                return $html;
            })
            ->filterColumn('related_to', function ($query, $keyword) {
                if (stripos('Project', $keyword) !== false) {
                    $query->where('related_to', 1);
                } elseif (stripos('Task', $keyword) !== false) {
                    $query->orWhere('related_to', 2);
                } elseif (stripos('Lead', $keyword) !== false) {
                    $query->orWhere('related_to', 3);
                } elseif (stripos('Deal', $keyword) !== false) {
                    $query->orWhere('related_to', 4);
                } elseif (stripos('Property', $keyword) !== false) {
                    $query->orWhere('related_to', 5);
                } elseif (stripos('Contract', $keyword) !== false) {
                    $query->orWhere('related_to', 6);
                } elseif (stripos('Other', $keyword) !== false) {
                    $query->orWhere('related_to', 7);
                }
            })
            ->editColumn('related_assign', function (BusinessProcessMapping $businessProcessMapping) {
                $related = Related::find($businessProcessMapping->related_to);
                $commaSeparatedString = '';

                if ($related !== null) {
                    if ($related->related == 'Other') {
                        $commaSeparatedString = $businessProcessMapping->related_assign;
                    } else {
                        $idsArray = explode(',', $businessProcessMapping->related_assign);
                        $value = [];

                        if (module_is_active('Project') && $related->related == 'Project') {
                            $value = Project::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        } elseif (module_is_active('Lead') && $related->related == 'Task') {
                            $value = Task::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('title')->toArray();
                        } elseif (module_is_active('Lead') && $related->related == 'Lead') {
                            $value = Lead::where('workspace_id', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        } elseif (module_is_active('Lead') && $related->related == 'Deal') {
                            $value = Deal::where('workspace_id', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        } elseif (module_is_active('PropertyManagement') && $related->related == 'Property') {
                            $value = EntitiesProperty::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        } elseif (module_is_active('Contract') && $related->related == 'Contract') {
                            $value = Contract::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('subject')->toArray();
                        }

                        $commaSeparatedString = implode(',', $value);
                    }
                }

                if (!empty($commaSeparatedString)) {
                    $html = '';
                    foreach (explode(',', $commaSeparatedString) as $relatedItem) {
                        $html .= '<span class="badge p-2 m-1 px-3 bg-primary">' . htmlspecialchars($relatedItem) . '</span>';
                    }
                    return $html;
                } else {
                    return '-';
                }
            });

        if (\Laratrust::hasPermission('businessprocessmapping edit') || \Laratrust::hasPermission('businessprocessmapping delete')) {
            $dataTable->addColumn('action', function (BusinessProcessMapping $businessProcessMapping) {
                return view('business-process-mapping::businessprocessmapping.action', compact('businessProcessMapping'));
            });

            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, BusinessProcessMapping $model): QueryBuilder
    {
        $businessProcess = $model->select('business_process_mappings.*')
            ->where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace());

        if ($request->module == 'Task') {
            $task = Task::where('project_id', $request->project_id)->pluck('id');
            $businessProcess->where('related_to', $request->related)
                ->where(function ($query) use ($task) {
                    foreach ($task as $value) {
                        $query->orWhereRaw("FIND_IN_SET(?, related_assign)", [$value]);
                    }
                });
        } elseif ($request->module == 'Lead') {
            if ($request->related !== null) {
                $businessProcess->where('related_to', $request->related);
            }
            if ($request->lead_id !== null) {
                $businessProcess->whereRaw("FIND_IN_SET(?, related_assign)", [$request->lead_id]);
            }
        } elseif ($request->module == 'Deal') {
            if ($request->related !== null) {
                $businessProcess->where('related_to', $request->related);
            }
            if ($request->deal_id !== null) {
                $businessProcess->whereRaw("FIND_IN_SET(?, related_assign)", [$request->deal_id]);
            }
        } elseif ($request->module == 'Property') {
            if ($request->related !== null) {
                $businessProcess->where('related_to', $request->related);
            }
            if ($request->property_id !== null) {
                $businessProcess->whereRaw("FIND_IN_SET(?, related_assign)", [$request->property_id]);
            }
        } elseif ($request->module == 'Contract') {
            if ($request->related !== null) {
                $businessProcess->where('related_to', $request->related);
            }
            if ($request->contract_id !== null) {
                $businessProcess->whereRaw("FIND_IN_SET(?, related_assign)", [$request->contract_id]);
            }
        } else {
            if ($request->related !== null) {
                $businessProcess->where('related_to', $request->related);
            }
            if ($request->project_id !== null) {
                $businessProcess->whereRaw("FIND_IN_SET(?, related_assign)", [$request->project_id]);
            }
        }

        return $businessProcess;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('business_process_mappings-table')
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
        $column = [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('title')->title(__('Title')),
            Column::make('description')->title(__('Description')),
            Column::make('related_to')->title(__('Related')),
            Column::make('related_assign')->title(__('Related To'))->searchable(false),
        ];
        if (\Laratrust::hasPermission('businessprocessmapping edit') || \Laratrust::hasPermission('businessprocessmapping delete')) {
            $action = [
                Column::computed('action')
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
        return 'business_process_mappings_' . date('YmdHis');
    }
}
