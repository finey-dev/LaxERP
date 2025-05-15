<?php

namespace Workdo\MarketingPlan\DataTables;

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
use App\Models\User;
use Workdo\MarketingPlan\Entities\MarketingPlan;

class MarketingPlanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumns = ['thumbnail_image','status','stage','challenge','role_id','user_id'];

        $dataTable = new EloquentDataTable($query);

        $dataTable
            ->addIndexColumn()
            ->editColumn('thumbnail_image', function (MarketingPlan $MarketingPlan) {
                $thumbnail_image = $MarketingPlan->thumbnail_image ? get_file($MarketingPlan->thumbnail_image) : asset("packages/workdo/MarketingPlan/src/Resources/assets/img/default.jpg");
                $html =  '<a href='. $thumbnail_image . ' target="_blank" class="image-fixsize"> 
                <img src=' . $thumbnail_image . ' class="rounded border-2 border border-primary">
                </a>';
                return $html;
            })
            ->editColumn('status', function (MarketingPlan $MarketingPlan) {
                return isset($MarketingPlan->Status) ? $MarketingPlan->Status->name : '-';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->whereHas('Status', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('stage', function (MarketingPlan $MarketingPlan) {
                return isset($MarketingPlan->Stage) ? $MarketingPlan->Stage->name : '-';
            })
            ->filterColumn('stage', function ($query, $keyword) {
                $query->whereHas('Stage', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('challenge', function (MarketingPlan $MarketingPlan) {
                return isset($MarketingPlan->Challenge) ? $MarketingPlan->Challenge->name : '-';
            })
            ->filterColumn('challenge', function ($query, $keyword) {
                $query->whereHas('Challenge', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('role_id', function (MarketingPlan $MarketingPlan) {
                return isset($MarketingPlan->Role) ? $MarketingPlan->Role->name : '--'  ;
            })
            ->filterColumn('role_id', function ($query, $keyword) {
                $query->whereHas('Role', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('user_id',function(MarketingPlan $MarketingPlan){
                $user_id = explode(',', $MarketingPlan->user_id);
                $users = User::whereIn('id', $user_id)->get();
                $html ='';
                foreach ($users as $user)
                {
                    if (check_file($user->avatar) == false) {
                        $path = asset('uploads/user-avatar/avatar.png');
                    } else {
                        $path = get_file($user->avatar);
                    }
                    $html .= '<img  src="' . $path . '" data-bs-toggle="tooltip"  title=" ' .$user->name . ' " data-bs-placement="top"  class="rounded-circle" width="25" height="25">';
                }
                return $html;
            });

        if (\Laratrust::hasPermission('businessplan edit') ||
            \Laratrust::hasPermission('businessplan show') ||
            \Laratrust::hasPermission('businessplan delete'))
            {
                $dataTable->addColumn('action', function (MarketingPlan $MarketingPlan) {
                    return view('marketing-plan::marketingplan.action', compact('MarketingPlan'));
                });
                $rawColumns[] = 'action';
            }

        return $dataTable->rawColumns($rawColumns);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MarketingPlan $model, Request $request)
    {
        $MarketingPlans = MarketingPlan::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

        return $MarketingPlans;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('marketing-plan-table')
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
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('thumbnail_image')->title(__('Thumbnail Image'))->searchable(false)->orderable(false)->exportable(false)->printable(false),
            Column::make('name')->title(__('Creativity Name')),
            Column::make('status')->title(__('Status')),
            Column::make('stage')->title(__('Stage')),
            Column::make('challenge')->title(__('Challenge')),
            Column::make('user_id')->title(__('Users'))->searchable(false)->orderable(false)->exportable(false)->printable(false),
            Column::make('role_id')->title(__('Role')),
            Column::make('rating')->title(__('Rating')),
        ];

            if (
                \Laratrust::hasPermission('marketing plan edit') ||
                \Laratrust::hasPermission('marketing plan show') ||
                \Laratrust::hasPermission('marketing plan delete')
            ) {
                $columns[] = Column::computed('action')
                    ->title(__('Action'))
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
        return 'MarketingPlans_' . date('YmdHis');
    }
}
