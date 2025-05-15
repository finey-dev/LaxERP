<?php

namespace Workdo\Sales\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Entities\SalesAccount;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesAccountDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name', 'website','user_id'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (SalesAccount $salesAccount) {
                $html = '';
                if (\Laratrust::hasPermission('salesaccount edit')) {
                    $html .= '<a href="' . route('salesaccount.edit', $salesAccount->id) . '" data-size="md" data-title="' . __('Account Details') . '" class="action-item text-primary">';
                    $html .= ucfirst($salesAccount->name);
                    $html .= '</a>';
                } else {
                    $html .= '<a href="#" data-size="md"
                    data-title="' . __('Account Details') . '" class="action-item text-primary">';
                    $html .= ucfirst($salesAccount->name);
                    $html .= '</a>';
                }
                return $html;
            })
            ->editColumn('website', function (SalesAccount $salesAccount) {
                $html = '';
                $html .= '<span class="budget">' . $salesAccount->website . ' ';
                $html .= '<a href="' . $salesAccount->website . '" target="_blank" class="btn btn-lg btn-sm d-inline-flex align-items-center">';
                $html .= '<i class="ti ti-external-link text-success" style="font-size: 1.5rem;"></i>';
                $html .= '</a>';
                $html .= '</span>';
                return $html;
            })
            ->editColumn('user_id', function (SalesAccount $salesAccount) {
                return ucfirst(optional($salesAccount->assign_user)->name ?? '-');
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('assign_user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });

            if (\Laratrust::hasPermission('salesaccount show') ||
            \Laratrust::hasPermission('salesaccount edit') ||
            \Laratrust::hasPermission('salesaccount delete'))
            {
                $dataTable->addColumn('action', function (SalesAccount $salesAccount) {
                    return view('sales::salesaccount.sales_account_action', compact('salesAccount'));
                });
                $rowColumn[] = 'action';
            }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesAccount $model)
    {
        return $model->where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('sales-account-table')
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
            Column::make('email')->title(__('Email')),
            Column::make('phone')->title(__('Phone')),
            Column::make('website')->title(__('Website')),
            Column::make('billing_address')->title(__('Billing Address'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('billing_city')->title(__('Billing City'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('billing_state')->title(__('Billing State'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('billing_country')->title(__('Billing Country'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('billing_postalcode')->title(__('Billing Postal Code'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('shipping_address')->title(__('Shipping Address'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('shipping_city')->title(__('Shipping City'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('shipping_state')->title(__('Shipping State'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('shipping_country')->title(__('Shipping Country'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('shipping_postalcode')->title(__('Shipping Postal Code'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('description')->title(__('Description'))->visible(false)->searchable(false)->orderable(false)->printable(false),
            Column::make('user_id')->title(__('Assign User')),
        ];

        if (\Laratrust::hasPermission('salesaccount show') ||
            \Laratrust::hasPermission('salesaccount edit') ||
            \Laratrust::hasPermission('salesaccount delete')) {
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
        return 'Accounts_' . date('YmdHis');
    }
}
