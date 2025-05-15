<?php

namespace Workdo\Procurement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Entities\RfxApplication;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RfxArchiveDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['rfx', 'stage', 'rating', 'proposal', 'created_at'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('rfx', function ($row) {
                return ucfirst($row->rfx_title);
            })
            ->editColumn('rating', function ($row) {
                $html = '<span class="static-rating static-rating-sm d-block">';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $row->rating) {
                        $html .= '<i class="star fas fa-star voted"></i>';
                    } else {
                        $html .= '<i class="star fas fa-star"></i>';
                    }
                }
                $html .= '</span>';
                return $html;
            })
            ->editColumn('proposal', function (RfxApplication $application) {
                if (check_file($application->proposal) == true) {
                    $path = get_file($application->proposal);

                    $html = '<span class="text-sm action-btn bg-primary ms-2">
                    <a class=" btn btn-sm align-items-center"
                        href="' . $path . '"
                        data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __("download") }}" download=""><i
                            class="ti ti-download text-white"></i></a>
                </span>';

                } else {
                    $html = '-';
                }


                return $html;
            })
            ->editColumn('created_at', function (RfxApplication $application) {
                $now = $application->created_at;
                return $now;
            });

        if (\Laratrust::hasPermission('rfxapplication show')) {
            $dataTable->addColumn('action', function (RfxApplication $application) {
                $html= '<div class="action-btn">
                            <a class="mx-3 btn bg-warning btn-sm  align-items-center" data-bs-toggle="tooltip" title="View" data-title="Details" href="' . route('rfx-application.show', \Crypt::encrypt($application->id)) . '"> <i class="ti ti-eye text-white"></i></a>
                        </div>';
                return $html;
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(RfxApplication $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select('rfx_applications.*', 'rfxs.title as rfx_title')
            ->leftJoin('rfxs', 'rfxs.id', '=', 'rfx_applications.rfx')
            ->where('rfx_applications.created_by', creatorId())
            ->where('rfx_applications.workspace', getActiveWorkSpace())
            ->where('rfx_applications.is_archive', 1);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('rfx-archive-table')
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
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('rfx')->title(__('Applied For'))->name('rfxs.title'),
            Column::make('rating')->title(__('Rating'))->searchable(false)->printable(false)->exportable(false),
            Column::make('created_at')->title(__('Applied at')),
            Column::make('proposal')->title(__('Proposal'))->searchable(false)->printable(false)->exportable(false),
        ];
        if (\Laratrust::hasPermission('rfxapplication show')) {
            $action = [
                Column::computed('action')->title(__('Action'))
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
        return 'Rfx_Archive_' . date('YmdHis');
    }
}
