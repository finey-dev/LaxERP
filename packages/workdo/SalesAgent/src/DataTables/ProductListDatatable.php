<?php

namespace Workdo\SalesAgent\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Workdo\ProductService\Entities\ProductService;
use Workdo\SalesAgent\Entities\Program;
use Workdo\SalesAgent\Entities\ProgramItems;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductListDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['image'];
        $dataTable =  (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('image', function (ProgramItems $item) {
                $path = (check_file($item->image) == false) ? asset('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg') : get_file($item->image);
                return '<a href="' . $path . '" target="_blank"><img src="' . $path . '" class="img-fluid rounded-circle card-avatar" width="35" id="blah3"></a>';
            })
            ->editColumn('name', function (ProgramItems $item) {
                return optional($item->productService)->name ?? '';
            })->filterColumn('name', function ($query, $keyword) {
                $query->whereHas('productService', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('sku', function (ProgramItems $item) {
                return optional($item->productService)->sku ?? '';
            })->filterColumn('sku', function ($query, $keyword) {
                $query->whereHas('productService', function ($q) use ($keyword) {
                    $q->where('sku', 'like', "%$keyword%");
                });
            })
            ->editColumn('sale_price', function (ProgramItems $item) {
                return optional($item->productService)->sale_price ?? '';
            })->filterColumn('sale_price', function ($query, $keyword) {
                $query->whereHas('productService', function ($q) use ($keyword) {
                    $q->where('sale_price', 'like', "%$keyword%");
                });
            })
            ->editColumn('type', function (ProgramItems $item) {
                return optional($item->productService)->type ?? '';
            })->filterColumn('type', function ($query, $keyword) {
                $query->whereHas('productService', function ($q) use ($keyword) {
                    $q->where('type', 'like', "%$keyword%");
                });
            });
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProgramItems $model, Request $request): QueryBuilder
    {
        $is_program_table = DB::table('sales_agents_programs')->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [Auth::user()->id])->pluck('id')->all();
        return $model->with('productService')->whereIn('program_id', ($request->program != null) ? [$request->program] : $is_program_table);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales_agents_programs-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var program = $("select[name=program]").val();
                    console.log(program);
                    d.program = program
                }',
            ])
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
                    if (!$("select[name=program]").val() && !$("select[name=category]").val()) {
                        toastrs("Error!", "Please select Atleast One Filter ", "error");
                        return;
                    }
                    $("#sales_agents_programs-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=program]").val("")
                    $("select[name=category]").val("")
                    $("#sales_agents_programs-table").DataTable().draw();
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
        return [
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('image')->title(__('Image'))->searchable(false)->orderable(false)->exportable(false),
            Column::make('name')->title(__('Name')),
            Column::make('sku')->title(__('Sku')),
            Column::make('sale_price')->title(__('Sale Price')),
            Column::make('type')->title(__('Type')),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductList_' . date('YmdHis');
    }
}
