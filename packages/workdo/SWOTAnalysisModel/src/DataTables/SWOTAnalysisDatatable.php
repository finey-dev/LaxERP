<?php

namespace Workdo\SWOTAnalysisModel\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\SWOTAnalysisModel\Entities\SwotAnalysisModel;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SWOTAnalysisDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['user_id', 'thumbnail_image', 'role_id', 'status', 'stage', 'challenge'];

        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('user_id', function (SwotAnalysisModel $swotanalysismodels) {
                $userIds = explode(',', $swotanalysismodels->user_id);
                $users = User::whereIn('id', $userIds)->get();
                $html = '';
                foreach ($users as $user) {
                    $avatar = $user->avatar ? get_file($user->avatar) : asset('packages/workdo/SWOTAnalysisModel/src/Resources/assets/img/avatar.png');
                    $html .= '<img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                             title="' . $user->name . '"
                             src="' . $avatar . '"
                             class="rounded-circle" width="25" height="25">';
                }
                return $html;
            })
            ->editColumn('thumbnail_image', function (SwotAnalysisModel $swotanalysismodels) {
                $thumbnail = $swotanalysismodels->thumbnail_image;
                $image = check_file($thumbnail) ? get_file($thumbnail) : asset('packages/workdo/SWOTAnalysisModel/src/Resources/assets/img/thumbnail-not-found.png');
                return '<a href="' . $image . '" target="_blank" class="image-fixsize"><img src="' . $image . '" class="rounded border-2 border border-primary"></a>';
            })
            ->editColumn('role_id', function (SwotAnalysisModel $swotanalysismodels) {
                return $swotanalysismodels->Role ? $swotanalysismodels->Role->name ?? '---' : '---';
            })
            ->editColumn('status', function (SwotAnalysisModel $swotanalysismodels) {
                return $swotanalysismodels->Status ? $swotanalysismodels->Status->name ?? '---' : '---';
            })
            ->editColumn('stage', function (SwotAnalysisModel $swotanalysismodels) {
                return $swotanalysismodels->Stage ? $swotanalysismodels->Stage->name ?? '---' : '---';
            })
            ->editColumn('challenge', function (SwotAnalysisModel $swotanalysismodels) {
                return $swotanalysismodels->Challenge ? $swotanalysismodels->Challenge->name ?? '---' : '----';
            });

        if (\Laratrust::hasPermission('SWOTAnalysisModel edit') || \Laratrust::hasPermission('SWOTAnalysisModel delete') || \Laratrust::hasPermission('SWOTAnalysisModel show')) {
            $dataTable->addColumn('action', function (SwotAnalysisModel $swotanalysismodels) {
                return view('swotanalysis-model::action', compact('swotanalysismodels'));
            });
            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);
    }


    public function query(SwotAnalysisModel $model): QueryBuilder
    {
        return $model
        ->with(['Status', 'Stage', 'Challenge', 'Role'])
        ->where('swot_analysis_models.created_by', '=', creatorId())
        ->where('swot_analysis_models.workspace', getActiveWorkSpace())
        ->select('swot_analysis_models.*')
        ->newQuery();

    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('swot_analysis_models-table')
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

                $("body").on("click", "#applyfilter", function() {
                    if (!$("select[name=item_type]").val() && !$("select[name=category]").val()) {
                        toastrs("Error!", "Please select At least One Filter", "error");
                        return;user_id
                    }
                    $("#swot_analysis_models-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=item_type]").val("");
                    $("select[name=category]").val("");
                    $("#swot_analysis_models-table").DataTable().draw();
                });

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
            Column::make('name')->title(__('Name')),
            Column::make('status')->title(__('Status'))->name('Status.name'),
            Column::make('stage')->title(__('Stage'))->name('Stage.name'),
            Column::make('challenge')->title(__('Challenge'))->name('Challenge.name'),
            Column::make('user_id')->title(__('Users'))->searchable(false)->orderable(false)->printable(false)->exportable(false),
            Column::make('role_id')->title(__('Role'))->name('Role.name'),
            Column::make('rating')->title(__('Rating')),
        ];

        if (\Laratrust::hasPermission('SWOTAnalysisModel edit') || \Laratrust::hasPermission('SWOTAnalysisModel delete') || \Laratrust::hasPermission('SWOTAnalysisModel show')) {
            $columns[] = Column::computed('action')
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
        return 'swot_analysis_models_' . date('YmdHis');
    }
}
