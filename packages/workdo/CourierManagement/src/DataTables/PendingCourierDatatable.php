<?php

namespace Workdo\CourierManagement\DataTables;

use App\Models\WorkSpace;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\Entities\CourierPackageInfo;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PendingCourierDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['tracking_id', 'tracking_status', 'is_approve', 'payment_status'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('tracking_id', function (CourierReceiverDetails $courier) {
                if (\Laratrust::hasPermission('courier pending request manage')) {
                    $url = route('show.courier.pending.request', ['trackingId' => encrypt($courier->tracking_id)]);
                } else {
                    $url = '#!';
                }
                $html = '<a class="btn btn-outline-primary" href="' . $url . '" > ' . $courier->tracking_id . '</a>';
                return $html;
            })
            ->editColumn('created_by', function (CourierReceiverDetails $courier) {
                return $courier->createdBy ? $courier->createdBy->name ?? '-' : '-';
            })
            ->filterColumn('is_approve', function ($query, $keyword) {
                if (stripos('pending', $keyword) !== false) {
                    return  $query->where('is_approve', null);
                }
                if (stripos('Rejected', $keyword) !== false) {
                    return  $query->where('is_approve', 0);
                }
            })
            ->editColumn('category', function (CourierReceiverDetails $courier) {
                return $courier->packageInformarmation->courier_category->category;
            })
            ->editColumn('is_approve', function (CourierReceiverDetails $courier) {
                if ($courier->is_approve === null) {
                    $color = 'bg-warning';
                    $statusName = __('Pending');
                } else if ($courier->is_approve == 0) {
                    $color = 'bg-danger';
                    $statusName = __('Rejected');
                } else {
                    $color = 'bg-success';
                    $statusName = __('Approved');
                }
                $html = '<span class="badge fix_badge ' . $color . ' p-2 px-3">' . $statusName . '</span>';
                return $html;
            })
            ->editColumn('created_at', function (CourierReceiverDetails $courier) {
                return  $courier->created_at->diffForHumans();
            });

        if ((\Laratrust::hasPermission('courier pending request approve')) || (\Laratrust::hasPermission('courier pending request reject'))  || (\Laratrust::hasPermission('courier delete'))) {
            $dataTable->addColumn('action', function (CourierReceiverDetails $courier) {
                return view('courier-management::pending_courier.action', compact('courier'));
            });
            $rowColumn[] = 'action';
        }

        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CourierReceiverDetails $model): QueryBuilder
    {
        $courierDetails = $model->with(['createdBy', 'packageInformarmation.courier_category', 'packageInformarmation.getTrackingStatus'])
            ->where('courier_receiver_details.workspace_id', '=', getActiveWorkSpace())
            ->where('courier_receiver_details.created_by', '=', creatorId())
            ->where(function ($query) {
                $query->where('is_approve', null)
                    ->orWhere('is_approve', 0);
            });

        return $courierDetails;
    }



    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('pending-courier-table')
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
            Column::make('id')->searchable(false)->exportable(false)->printable(false)->visible(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('tracking_id')->title(__('Tracking ID')),
            Column::make('sender_name')->title(__('Sender Name')),
            Column::make('created_by')->title(__('Created By'))->name('createdBy.name')->orderable(false),
            Column::make('is_approve')->title(__('Request Status')),
            Column::make('created_at')->title(__('Created At')),
        ];

        if ((\Laratrust::hasPermission('courier pending request approve')) || (\Laratrust::hasPermission('courier pending request reject'))  || (\Laratrust::hasPermission('courier delete'))) {
            $action =  [
                Column::computed('action')
                    ->title(__('Action'))
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
        return 'PendingCourier_' . date('YmdHis');
    }
}
