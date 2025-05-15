<?php

namespace Workdo\Recruitment\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\Recruitment\Entities\JobApplication;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobArchiveDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name', 'job', 'rating', 'created_at', 'resume'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (JobApplication $jobApplication) {
                $url = route('job-application.show', Crypt::encrypt($jobApplication->id));
                $html = '<a class="btn btn-outline-primary" href="' . $url . '">
                                        ' . $jobApplication->name . '
                                    </a>';
                return $html;
            })
            ->editColumn('job', function (JobApplication $jobApplication) {
                return $jobApplication->job ? $jobApplication->jobs->title ?? '-' : '-';
            })
            ->editColumn('rating', function ($application) {
                $html = '<span class="static-rating static-rating-sm d-block">';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $application->rating) {
                        $html .= '<i class="star fas fa-star voted"></i>';
                    } else {
                        $html .= '<i class="star fas fa-star"></i>';
                    }
                }
                $html .= '</span>';
                
                return $html;
            })
            ->editColumn('created_at', function (JobApplication $jobApplication) {
                return company_date_formate($jobApplication->created_at) ?? '-';
            })
            ->editColumn('resume', function ($application) {
                if (!empty($application->resume)) {
                    $html = '<span class="text-sm action-btn me-2">';
                    $html .= '<a class="btn btn-sm align-items-center bg-primary" href="' . get_file($application->resume) . '"';
                    $html .= ' data-bs-toggle="tooltip" data-bs-original-title="' . __('download') . '" download>';
                    $html .= '<i class="ti ti-download text-white"></i></a>';
                    $html .= '</span>';

                    $html .= '<div class="action-btn me-2">';
                    $html .= '<a class="mx-3 btn btn-sm align-items-center  bg-secondary" href="' . get_file($application->resume) . '" target="_blank">';
                    $html .= '<i class="ti ti-crosshair text-white" data-bs-toggle="tooltip" data-bs-original-title="' . __('Preview') . '"></i>';
                    $html .= '</a></div>';
                } else {
                    $html = '-';
                }

                return $html;
            })
            ->filterColumn('job', function ($query, $keyword) {
                $query->whereHas('jobs', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('jobapplication show')) {
                $dataTable->addColumn('action', function (JobApplication $jobApplication) {
                    return view('recruitment::jobApplication.button-archive', compact('jobApplication'));
                });
                $rowColumn[] = 'action';
            }
            return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobApplication $model, Request $request): QueryBuilder
    {
        $archive_application = $model->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('is_archive', 1);

        return $archive_application;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('job-archive-table')
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
            Column::make('name')->title(__('Name')),
            Column::make('job')->title(__('Applied For')),
            Column::make('rating')->title(__('Rating'))->searchable(false)->exportable(false)->printable(false),
            Column::make('created_at')->title(__('Applied at')),
            Column::make('resume')->title(__('Resume'))->searchable(false)->exportable(false)->printable(false),
        ];
        if (
            \Laratrust::hasPermission('jobapplication show')
        ) {
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
        return 'Job Archives_' . date('YmdHis');
    }
}
