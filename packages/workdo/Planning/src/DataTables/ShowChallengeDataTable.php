<?php

namespace Workdo\Planning\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Planning\Entities\PlanningCharters;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ShowChallengeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['thumbnail_image', 'user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('thumbnail_image', function (PlanningCharters $charter) {
                if (check_file($charter->thumbnail_image) == false) {
                    $path = asset('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg');
                } else {
                    $path = get_file($charter->thumbnail_image);
                }
                $html = '<a href="'. $path .'" target="_blank">
                            <img src="' . $path . '" class="rounded border-2 border border-primary
                            " style="width:70px;" id="blah3">
                        </a>';

                return $html;
            })
            ->editColumn('status', function (PlanningCharters $charter) {
                return $charter->statuses ? $charter->statuses->name ?? '--' : '---';
            })
            ->editColumn('stage', function (PlanningCharters $charter) {
                return $charter->stages ? $charter->stages->name ?? '--' : '---';
            })
            ->editColumn('user_id', function (PlanningCharters $charters) {
                $userIds = explode(',', $charters->user_id);
                $users = User::whereIn('id', $userIds)->get();
                $html = '';
                foreach ($users as $user) {
                    $avatar = $user->avatar ? get_file($user->avatar) : get_file('avatar.png');
                    $html .= '<img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                             title=' . $user->name . '
                            src=' . $avatar . '
                            class="rounded-circle" width="25" height="25">';
                }
                return $html;
            })
            ->editColumn('role_id', function (PlanningCharters $charters) {
                return $charters->roles ? $charters->roles->name ?? '---' : '---';
            })
            ->editColumn('rating', function (PlanningCharters $charters) {
                return $charters->rating;
            });

        if ((\Laratrust::hasPermission('charters delete')) || (\Laratrust::hasPermission('charters edit')) || (\Laratrust::hasPermission('charters show'))) {
            $dataTable->addColumn('action', function (PlanningCharters $charters) {
                return view('planning::planningchallenges.show_challenges_action', compact('charters'));
            });

            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PlanningCharters $model): QueryBuilder
    {
        $query = $model
            ->with(['statuses', 'stages', 'roles'])
            ->where('challenge', $this->challengeId)
            ->where('planning_charters.created_by', '=', creatorId())
            ->where('planning_charters.workspace', getActiveWorkSpace())
            ->newQuery();
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('showchallenge-table')
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
            Column::make('id')->searchable(false)->exportable(false)->printable(false)->visible(false),
            Column::make('charter_name')->title(__('Name')),
            Column::make('thumbnail_image')->title(__('Image'))->orderable(false),
            Column::make('status')->title(__('Status'))->name('statuses.name'),
            Column::make('stage')->title(__('Stage'))->name('stages.name'),
            Column::make('user_id')->title(__('Users'))->orderable(false),
            Column::make('role_id')->title(__('Role'))->name('roles.name'),
            Column::make('rating')->title(__('Rating')),
        ];

        if ((\Laratrust::hasPermission('charters delete')) || (\Laratrust::hasPermission('charters edit')) || (\Laratrust::hasPermission('charters show'))) {
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
        return 'Show_Challenge_' . date('YmdHis');
    }
}
