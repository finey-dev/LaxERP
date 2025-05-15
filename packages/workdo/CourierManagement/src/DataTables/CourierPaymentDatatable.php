<?php

namespace Workdo\CourierManagement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\Entities\CourierPackagePayment;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CourierPaymentDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['tracking_id', 'tracking_status', 'payment_status', 'payment_receipt'];
        $dataTable =  (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('tracking_id', function (CourierPackagePayment $courier) {
                $html = '<a class="btn btn-outline-primary" href="#" > ' . $courier->tracking_id . '</a>';
                return $html;
            })
            ->editColumn('payment_date', function (CourierPackagePayment $courier) {
                $paymentDate = Carbon::parse($courier->payment_date);
                $formattedDate = $paymentDate->format('d/m/Y');
                return $formattedDate;
            })
            ->editColumn('sender_name', function (CourierPackagePayment $courier) {
                return  $courier->getCourierInformation ?  $courier->getCourierInformation->sender_name ?? '-' :  '--';
            })
            ->editColumn('package_category', function (CourierPackagePayment $courier) {
                return $courier->getCourierInformation->packageInformarmation->courier_category->category ?? '-';
            })
            ->editColumn('tracking_status', function (CourierPackagePayment $courier) {
                $statusColor = $courier->getCourierInformation->packageInformarmation->getTrackingStatus->status_color ?? '04FF04';
                $statusName = $courier->getCourierInformation->packageInformarmation->getTrackingStatus->status_name ??  'pending';
                $html = '<span class="badge fix_badge p-2 px-3" style="background-color:' . '#' . $statusColor . '">
             ' . $statusName ?? 'pending' . '
            </span>';
                return $html;
            })
            ->editColumn('payment_status', function (CourierPackagePayment $courier) {
                $paymentStatus = $courier->getCourierInformation->payment_status ?? 'pending';
                if ($paymentStatus == 'pending') {
                    $color = 'bg-warning';
                } else {
                    $color = 'bg-success';
                }
                $html = '<span class="badge fix_badge ' . $color . '  p-2 px-3">' . $paymentStatus . '</span>';
                return $html;
            })
            ->editColumn('payment_receipt', function (CourierPackagePayment $courier) {
                $html = " <div class='action-btn'>
                    <a download='' href='" . (!empty($courier->payment_receipt) ? get_file($courier->payment_receipt) : asset('packages/workdo/CourierManagement/src/Resources/assets/images/default.png')) . "'
                        class='btn-primary btn btn-sm align-items-center' data-bs-toggle='tooltip' title='" . __('Download') . "'>
                        <i class='ti ti-download'></i>
                    </a>
                </div>";
                return $html;
            });
        if ((\Laratrust::hasPermission('courier edit')) || (\Laratrust::hasPermission('courier delete'))) {
            $dataTable->addColumn('action', function (CourierPackagePayment $courier) {
                return view('courier-management::payment_information.action', compact('courier'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }


    public function query(CourierPackagePayment $model, Request $request): QueryBuilder
    {
        $courierData = $model->with([
            'getCourierInformation',
            'getCourierInformation.packageInformarmation',
            'getCourierInformation.packageInformarmation.courier_category',
            'getCourierInformation.packageInformarmation.getTrackingStatus'
        ])
            ->where('courier_package_payments.workspace_id', getActiveWorkSpace())
            ->where('courier_package_payments.created_by', creatorId());

        if (!empty($request->selected_date)) {
            $dateRange = $request->selected_date;
            list($startDate, $endDate) = explode(' to ', $dateRange);
            $startDateTime = Carbon::parse($startDate)->startOfDay();
            $endDateTime = Carbon::parse($endDate)->endOfDay();
            $courierData = $courierData->whereBetween('payment_date', [$startDateTime, $endDateTime]);
        }
        if (!empty($request->tracking_status)) {
            $courierData->whereHas('getCourierInformation.packageInformarmation', function ($query) use ($request) {
                $query->where('tracking_status', $request->tracking_status);
            });
        }

        return $courierData;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('courier-payment-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var selected_date = $("input[name=date]").val();
                    d.selected_date = selected_date

                    var tracking_status = $("select[name=tracking_status]").val();
                    d.tracking_status = tracking_status
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

                    if (!$("input[name=date]").val() && !$("select[name=tracking_status]").val()) {
                        toastrs("Error!", "Please select Atleast One Filter ", "error");
                        return;
                    }

                    $("#courier-payment-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("input[name=date]").val("")
                    $("select[name=tracking_status]").val("Select Tracking Status")
                    $("#courier-payment-table").DataTable().draw();
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
        $column = [
            Column::make('id')->searchable(false)->exportable(false)->printable(false)->visible(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('tracking_id')->title(__('Tracking ID')),
            Column::make('payment_date')->title(__('Payment Date'))->orderable(false),
            Column::make('sender_name')->title(__('Sender Name'))->name('getCourierInformation.sender_name'),
            Column::make('package_category')->title(__('Category'))->name('getCourierInformation.packageInformarmation.courier_category.category'),
            Column::make('price')->title(__('Price')),
            Column::make('tracking_status')->title(__('Tracking Status'))->name('getCourierInformation.packageInformarmation.getTrackingStatus.status_name'),
            Column::make('payment_type')->title(__('Payment Type')),
            Column::make('payment_status')->title(__('Payment Status'))->name('getCourierInformation.payment_status'),
            Column::make('payment_receipt')->title(__('Receipt'))->orderable(false),
        ];
        if ((\Laratrust::hasPermission('courier edit')) || (\Laratrust::hasPermission('courier delete'))) {
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
        return 'CourierPayment_' . date('YmdHis');
    }
}
