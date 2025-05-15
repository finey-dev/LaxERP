<?php

namespace Workdo\FileSharing\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Workdo\FileSharing\Entities\FileDownload;
use Workdo\FileSharing\Entities\FileShare;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FileTrashDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumns = ['file_status', 'user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('file_status', function (FileShare $fileShare) {
                if ($fileShare->file_status == 'Available') {
                    $html = '<span class="badge fix_badges bg-primary p-2 px-3">' . __($fileShare->file_status) . '</span>';
                } elseif ($fileShare->file_status == 'Not Available') {
                    $html = '<span class="badge fix_badges bg-info p-2 px-3">' . __($fileShare->file_status) . '</span>';
                }
                return $html;
            })

            ->editColumn('user_id', function (FileShare $fileShare) {
                $user_id = explode(',', $fileShare->user_id);
                $users = User::whereIn('id', $user_id)->get();
                $html = '';
                foreach ($users as $user) {
                    if (!empty($user->avatar) && file_exists(get_file($user->avatar))) {
                        $avatar = get_file($user->avatar);
                    } else {
                        $avatar = asset('packages/workdo/FileSharing/src/Resources/assets/img/avatar.png');
                    }
                    $html .= '<img alt="image" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $user->name . '" src="' . $avatar . '" class="rounded-circle" width="25" height="25">';
                }
                return $html;
            })
            ->editColumn('deleted_at', function (FileShare $fileShare) {
                return isset($fileShare->deleted_at) ? company_datetime_formate($fileShare->deleted_at) : '-';
            });

        if (\Laratrust::hasPermission('files trash-delete') || \Laratrust::hasPermission('files trash-restore')) {
            $dataTable->addColumn('action', function (FileShare $fileShare) {
                return view('file-sharing::trash.action', compact('fileShare'));
            });
            $rawColumns[] = 'action';
        }
        return $dataTable->rawColumns($rawColumns);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FileShare $model, Request $request): QueryBuilder
    {
        $query = $model::where('file_shares.created_by', creatorId())->where('file_shares.workspace', getActiveWorkSpace())->orderBy('file_shares.id', 'desc')->onlyTrashed();

        if (!empty($request->file_status)) {
            $query->where('file_status', '=', $request->file_status);
        }
        if (!empty($request->user)) {
            $query->whereRaw('FIND_IN_SET(?, user_id)', [$request->user]);
        }

        if (!empty($request->delet_date)) {
            $date_range = explode('to', $request->delet_date);
            if (count($date_range) == 2) {
                $query->whereBetween('deleted_at', $date_range);
            } else {
                $query->where('deleted_at', $date_range[0]);
            }
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('file_shares-table')
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
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('file_name')->title(__('File Name')),
            Column::make('file_size')->title(__('File Size')),
            Column::make('file_status')->title(__('Status')),
            Column::make('auto_destroy')->title(__('Auto Destroy')),
            Column::make('filesharing_type')->title(__('Share Mode')),
            Column::make('user_id')->title(__('Users'))->searchable(false)->exportable(false)->printable(false)->orderable(false),  
            Column::make('deleted_at')->title(__('Delete Date')),

        ];
        if (\Laratrust::hasPermission('files trash-delete') || \Laratrust::hasPermission('files trash-restore')) {
            $action = [
                Column::computed('action')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)->title(__('Action'))
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
        return 'file_shares_trash_' . date('YmdHis');
    }
}
