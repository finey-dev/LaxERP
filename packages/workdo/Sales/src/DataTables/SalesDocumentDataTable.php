<?php

namespace Workdo\Sales\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Entities\SalesDocument;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesDocumentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name','attachment', 'status','created_at','user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (SalesDocument $document) {
                $html = '';
                if (\Laratrust::hasPermission('salesdocument show')) {
                    $html.= '<a data-url="'. route("salesdocument.show", $document->id) .'" href="#" data-ajax-popup="true" data-size="md" data-title="'. __("Sales Documents Details") .'" class="action-item text-primary">';
                    $html.= ucfirst($document->name);
                    $html.= '</a>';
                } else {
                    $html.= '<a href="#" data-size="md" data-title="'. __("Sales Documents Details") .'" class="action-item text-primary">';
                    $html.= ucfirst($document->name);
                    $html.= '</a>';
                }
                return $html;
            })
            ->editColumn('attachment', function (SalesDocument $document) {
                $html = '';
                if (!empty($document->attachment)) {
                    $html.= '<a href="'. get_file($document->attachment) .'" download=""><i class="ti ti-download"></i></a>';
                } else {
                    $html.= '<span>'. __("No File") .'</span>';
                }
                return $html;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('Active', $keyword) !== false) {
                    $query->where('status', 0);
                }
                elseif (stripos('Draft', $keyword) !== false) {
                    $query->orWhere('status', 1);
                }
                elseif (stripos('Expired', $keyword) !== false) {
                    $query->orWhere('status', 2);
                }
                elseif (stripos('Canceled', $keyword) !== false) {
                    $query->orWhere('status', 3);
                }
            })
            ->editColumn('status', function (SalesDocument $document) {
                $status = [
                    0 => 'bg-success',
                    1 => 'bg-warning',
                    2 => 'bg-danger',
                    3 => 'bg-danger',
                ];

                $class = isset($status[$document->status]) ? $status[$document->status] : '';
                return '<span class="badge ' . $class . ' p-2 px-3" style="width: 73px;">' . SalesDocument::$status[$document->status] . '</span>';
            })
            ->editColumn('created_at', function (SalesDocument $document) {
                return company_date_formate($document->created_at);
            })
            ->editColumn('user_id', function (SalesDocument $document) {
                return ucfirst(optional($document->assign_user)->name ?? '-');
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('assign_user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
            if (\Laratrust::hasPermission('salesdocument show') ||
                \Laratrust::hasPermission('salesdocument edit') ||
                \Laratrust::hasPermission('salesdocument delete')) {
                $dataTable->addColumn('action', function (SalesDocument $document) {
                    return view('sales::document.document_action', compact('document'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesDocument $model)
    {
       return $model->where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales-document-table')
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
            Column::make('name')->title(__('Name')),
            Column::make('attachment')->title(__('File'))->searchable(false)->orderable(false)->exportable(false)->printable(false),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::make('user_id')->title(__('Assign User')),
        ];

        if (\Laratrust::hasPermission('salesdocument show') ||
            \Laratrust::hasPermission('salesdocument edit') ||
            \Laratrust::hasPermission('salesdocument delete')) {
            $columns[] = Column::computed('action')
                ->title(__('Action'))
                ->searchable(false)
                ->orderable(false)
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
        return 'SalesDocument_' . date('YmdHis');
    }
}
