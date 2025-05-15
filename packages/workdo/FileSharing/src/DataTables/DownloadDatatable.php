<?php

namespace Workdo\FileSharing\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\FileSharing\Entities\FileDownload;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DownloadDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rawColumns = ['fileName', 'ip_address', 'date', 'country', 'device_type', 'os_name'];

        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('fileName', function (FileDownload $fileDownload) {
                $originalString = $fileDownload->file_path;
                $substringToRemove = 'uploads/filesshare/';
                return str_replace($substringToRemove, '', $originalString);
            })
            ->addColumn('ip_address', function (FileDownload $fileDownload) {
                return !empty($fileDownload->ip_address) ? $fileDownload->ip_address : '************';
            })
            ->addColumn('date', function (FileDownload $fileDownload) {
                return !empty($fileDownload->date) ? company_datetime_formate($fileDownload->date) : '-';
            })
            ->addColumn('country', function (FileDownload $fileDownload) {
                $file_download = json_decode($fileDownload->details);
                return !empty($file_download->country) ? $file_download->country : '-';
            })
            ->addColumn('device_type', function (FileDownload $fileDownload) {
                $file_download = json_decode($fileDownload->details);
                return !empty($file_download->device_type) ? $file_download->device_type : '-';
            })
            ->addColumn('os_name', function (FileDownload $fileDownload) {
                $file_download = json_decode($fileDownload->details);
                return !empty($file_download->os_name) ? $file_download->os_name : '-';
            });
        if (\Laratrust::hasPermission('downloads show')) {
            $dataTable->addColumn('action', function (FileDownload $fileDownload) {
                return view('file-sharing::download.action', compact('fileDownload'));
            });
            $rawColumns[] = 'action';
        }
        return $dataTable->rawColumns($rawColumns);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FileDownload $model): QueryBuilder
    {
        $fileDownload = $model::where('file_downloads.created_by', creatorId())->where('file_downloads.workspace', getActiveWorkSpace())->orderBy('file_downloads.id', 'desc');

        return $fileDownload;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('file_downloads-table')
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
            Column::make('fileName')->title(__('Downloaded Files')),
            Column::make('ip_address')->title(__('Ip Address')),
            Column::make('date')->title(__('Last Download')),
            Column::make('country')->title(__('Country')),
            Column::make('device_type')->title(__('Device')),
            Column::make('os_name')->title(__('OS')),
        ];
        if (\Laratrust::hasPermission('downloads show')) {
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
        return 'file_downloads_' . date('YmdHis');
    }
}
