<?php

namespace Workdo\MeetingHub\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Entities\MeetingHubMeeting;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MeetingHubDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['sub_module', 'meeting_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('sub_module', function (MeetingHubMeeting $meeting) {
                return $meeting->submodules ? $meeting->submodules->submodule ?? '-' : '-';
            })
            ->filterColumn('meetinghub_meetings.sub_module', function ($query, $keyword) {
                $query->whereHas('submodules', function ($q) use ($keyword) {
                    $q->where('submodule', 'like', "%$keyword%");
                });
            })
            ->addColumn('meeting_id', function (MeetingHubMeeting $meeting) {
                return MeetingHubMeeting::getSubModuleUser()[$meeting->id];
            })
            ->filterColumn('meetinghub_meetings.id', function ($query, $keyword) {
                $meeting = MeetingHubMeeting::getSubModuleUser();
                $matchedKeys = [];
                foreach ($meeting as $key => $meetingUser){
                    if (stripos($meetingUser, $keyword) !== false) {
                        $matchedKeys[] = $key;
                    }
                }
                $query->whereIn('meetinghub_meetings.id', $matchedKeys);
            })
            ->filterColumn('meetinghub_meetings.meeting_type', function ($query, $keyword) {
                $query->where('meetinghub_meeting_types.name', 'like', "%$keyword%");
            });

            if (\Laratrust::hasPermission('meetinghub edit') ||
            \Laratrust::hasPermission('meetinghub delete'))
            {
                $dataTable->addColumn('action', function (MeetingHubMeeting $meeting) {
                    return view('meeting-hub::meeting.meeting_hub_action', compact('meeting'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MeetingHubMeeting $model): QueryBuilder
    {
        return $model->with('workspace')
                ->select([
                    'meetinghub_meetings.*',
                    'meetinghub_meeting_types.name as meeting_type',
                ])
                ->join('meetinghub_meeting_types', 'meetinghub_meeting_types.id', '=', 'meetinghub_meetings.meeting_type')
                ->where('meetinghub_meetings.workspace_id', getActiveWorkSpace());

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('meeting-hub-table')
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
            Column::make('sub_module')->title(__('Module'))->name('meetinghub_meetings.sub_module'),
            Column::make('caller')->title(__('From')),
            Column::make('meeting_id')->title(__('Contact'))->name('meetinghub_meetings.id'),
            Column::make('meeting_type')->title(__('Meeting Type'))->name('meetinghub_meetings.meeting_type'),
            Column::make('subject')->title(__('Subject')),
            Column::make('location')->title(__('Location')),
        ];

        if (\Laratrust::hasPermission('meetinghub edit') ||
            \Laratrust::hasPermission('meetinghub delete')) {
            $action = [
                Column::computed('action')
                    ->title(__('Action'))
                    ->searchable(false)
                    ->orderable(false)
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
        return 'Meeting_Hub_' . date('YmdHis');
    }
}
