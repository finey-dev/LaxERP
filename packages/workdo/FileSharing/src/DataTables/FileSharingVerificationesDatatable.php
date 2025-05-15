<?php

namespace Workdo\FileSharing\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Workdo\FileSharing\Entities\FileSharingVerification;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FileSharingVerificationesDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query)
    {
        $rawColumn = ['applied_date', 'action_date', 'status', 'attachment'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('applied_date', function (FileSharingVerification $verification) {
                return isset($verification->applied_date) ? company_datetime_formate($verification->applied_date) : '-';
            })
            ->editColumn('action_date', function (FileSharingVerification $verification) {
                return isset($verification->action_date) ? company_datetime_formate($verification->action_date) : '-';
            })
            ->editColumn('status', function (FileSharingVerification $verification) {
                if ($verification->status == 0) {
                    $html = '<span class="badge bg-warning p-2 px-3 text-white">' . __(FileSharingVerification::$statues[$verification->status]) . '</span>';
                } elseif ($verification->status == 1) {
                    $html = '<span class="badge bg-success p-2 px-3 text-white">' . __(FileSharingVerification::$statues[$verification->status]) . '</span>';
                } elseif ($verification->status == 2) {
                    $html =  '<span class="badge bg-danger p-2 px-3 text-white">' . __(FileSharingVerification::$statues[$verification->status]) . '</span>';
                } else {
                    $html = '-';
                }

                return $html;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Pending', $keyword) !== false) {
                    $query->where('status', 0);
                } elseif (stripos('Approved', $keyword) !== false) {
                    $query->orWhere('status', 1);
                } elseif (stripos('Reject', $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
            })
            ->editColumn('attachment', function (FileSharingVerification $verification) {
                if (!empty($verification->attachment) && check_file($verification->attachment)) {
                    $html = '<div class="action-btn bg-primary ms-2">
                                <a class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="' . __("Download") . ' "
                                    href="' . get_file($verification->attachment) . '" download>
                                    <i class="ti ti-download text-white"></i>
                                </a>
                            </div>';
                } else {
                    $html = '-';
                }

                return $html;
            });
        if (Auth::user()->type == 'super admin') {
            $dataTable->addColumn('user_id', function (FileSharingVerification $verification) {
                return isset($verification->User) ? $verification->User->name : '-';
            });
            $dataTable->addColumn('workspace', function (FileSharingVerification $verification) {
                return isset($verification->Workspace) ? $verification->Workspace->name : '-';
            });

            // Add Filter Logic for Related Fields
            $dataTable->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('User', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            });
            $dataTable->filterColumn('workspace', function ($query, $keyword) {
                $query->whereHas('Workspace', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            });

            $rawColumn[] = ['user_id', 'workspace'];
        }
        if (\Laratrust::hasPermission('verification edit') || \Laratrust::hasPermission('verification delete')) {
            $dataTable->addColumn('action', function (FileSharingVerification $verification) {

                return view('file-sharing::verification.action', compact('verification'));
            });
            $rawColumn[] = 'action';
        }
        return $dataTable->rawColumns($rawColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FileSharingVerification $model): QueryBuilder
    {
        $verifications =  $model::with(['Workspace', 'User']);
        return $verifications;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('file_sharing_verifications-table')
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
        if (Auth::user()->type == 'super admin') {
            $column =   [
                Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
                Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
                Column::make('user_id')->title(__('Company Name')),
                Column::make('workspace')->title(__('Workspace Name')),
                Column::make('applied_date')->title(__('Applied Date')),
                Column::make('action_date')->title(__('Action Date')),
                Column::make('status')->title(__('Status')),
                Column::make('attachment')->title(__('Attachment')),

            ];
        }
        if (\Laratrust::hasPermission('verification edit') || \Laratrust::hasPermission('verification delete')) {
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
        return 'File_sharing_verifications_' . date('YmdHis');
    }
}
