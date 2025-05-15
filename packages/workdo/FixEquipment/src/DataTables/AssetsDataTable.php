<?php

namespace Workdo\FixEquipment\DataTables;

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
use Workdo\FixEquipment\Entities\FixAsset;

class AssetsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['asset_image','category','purchase_price','purchase_date'];
        $dataTable = (new EloquentDataTable($query))
        ->addIndexColumn()
        ->editColumn('asset_image', function (FixAsset $asset ) {
            if (check_file($asset->asset_image) == false) {
                $path   = asset('packages/workdo/Fixequipemnt/src/Resources/assets/image/defualt.png');
            } else {
                $path   = get_file($asset->asset_image);
            }
            $html       = '<a href="' . $path . '" target="_blank" class="image-fixsize"><img src="' . $path . '" class="rounded border-2 border border-primary" width="35"></a>';
            return $html;

        })
        ->editColumn('category', function (FixAsset $asset) {
            return optional($asset->equipmentCategory)->title ?? '';
        })
        ->editColumn('purchase_price', function (FixAsset $asset) {
            return currency_format_with_sym($asset->purchase_price);
        })
        ->editColumn('purchase_date', function (FixAsset $asset) {
            return company_date_formate($asset->purchase_date);
        })
        ->filterColumn('purchase_date', function ($query, $keyword) {
            try {
                if (\Carbon\Carbon::hasFormat($keyword, 'd-m-Y')) {
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)->format('Y-m-d');
                    return $query->where('purchase_date', 'LIKE', "%$date%");
                } elseif (\Carbon\Carbon::hasFormat($keyword, 'm-Y')) {
                    $date = \Carbon\Carbon::createFromFormat('m-Y', $keyword)->format('Y-m');
                    return $query->where('purchase_date', 'LIKE', "%$date%");
                } elseif (\Carbon\Carbon::hasFormat($keyword, 'd-m')) {
                    $date = \Carbon\Carbon::createFromFormat('d-m', $keyword)->format('m-d');
                    return $query->where('purchase_date', 'LIKE', "%$date%");
                } else {
                    $hasDay = false;
                    $hasMonth = false;
                    $hasYear = false;
                    if (\Carbon\Carbon::hasFormat($keyword, 'd') && strlen($keyword) <= 2) {
                        $day = \Carbon\Carbon::createFromFormat('d', $keyword)->format('d');
                        $query->whereRaw('DAY(purchase_date) = ?', [$day]);
                        $hasDay = true;
                    }
                    if (\Carbon\Carbon::hasFormat($keyword, 'm') && strlen($keyword) <= 2) {
                        $month = \Carbon\Carbon::createFromFormat('m', $keyword)->format('m');
                        $query->orWhereRaw('MONTH(purchase_date) = ?', [$month]);
                        $hasMonth = true;
                    }
                    if (preg_match('/^\d{4}$/', $keyword)) {
                        $year = \Carbon\Carbon::createFromFormat('Y', $keyword)->format('Y');
                        $query->orWhereRaw('YEAR(purchase_date) = ?', [$year]);
                        $hasYear = true;
                    }

                    if ($hasDay || $hasMonth || $hasYear) {
                        return $query;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Invalid date format: ' . $keyword);
            }
        });



        if (\Laratrust::hasPermission('fix equipment assets edit') ||
            \Laratrust::hasPermission('fix equipment assets delete') ||
            \Laratrust::hasPermission('fix equipment assets manage')) {

            $dataTable->addColumn('action', function (FixAsset $asset) {
                return view('fix-equipment::assets.action', compact('asset'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    public function query(FixAsset $model)
    {
        $assets = $model->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->with('equipmentCategory');
        return $assets;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('assets-table')
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
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('asset_image')->title(__('Asset')),
            Column::make('title')->title(__('Name')),
            Column::make('category')->title(__('Category'))->name('equipmentCategory.title'),
            Column::make('purchase_price')->title(__('Purchase Price')),
            Column::make('purchase_date')->title(__('Purchase Date')),

        ];
        if (\Laratrust::hasPermission('fix equipment assets edit') ||
            \Laratrust::hasPermission('fix equipment assets delete') ||
            \Laratrust::hasPermission('fix equipment assets manage')) {

            $action = [
                Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)

                ->title(__('Action'))
            ];
            $column = array_merge($column,$action);
        }
        return $column;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Assets_' . date('YmdHis');
    }
}
