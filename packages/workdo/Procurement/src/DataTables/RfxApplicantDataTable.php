<?php

namespace Workdo\Procurement\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Entities\RfxApplicant;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RfxApplicantDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['profile', 'proposal'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('profile', function (RfxApplicant $applicant) {
                if (check_file($applicant->profile) == true) {
                    $path = get_file($applicant->profile);

                    $html = '<div class="action-btn  me-2">
                                                    <a class="bg-primary mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Download"
                                                        href="' . $path . '" download>
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn ">
                                                    <a class="mx-3 btn bg-secondary btn-sm align-items-center"
                                                        href="' . $path . '" target="_blank"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="Preview">
                                                        <i class="ti ti-crosshair text-white"></i>
                                                    </a>
                                                </div>';
                } else {
                    $html = '-';
                }
                return $html;
            })
            ->editColumn('proposal', function (RfxApplicant $applicant) {
                if (check_file($applicant->proposal) == true) {
                    $path = get_file($applicant->proposal);

                    $html = '<div class="action-btn  me-2">
                                                    <a class="bg-primary mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Download"
                                                        href="' . $path . '" download>
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn ">
                                                    <a class="mx-3 btn bg-secondary btn-sm align-items-center"
                                                        href="' . $path . '" target="_blank"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="Preview">
                                                        <i class="ti ti-crosshair text-white"></i>
                                                    </a>
                                                </div>';
                } else {
                    $html = '-';
                }
                return $html;
            });
        if (\Laratrust::hasPermission('rfx applicant edit') || \Laratrust::hasPermission('rfx applicant delete')) {
            $dataTable->addColumn('action', function (RfxApplicant $applicant) {
                return view('procurement::rfxApplicant.action', compact('applicant'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(RfxApplicant $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery()
            ->where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace());

        if (isset($request->name) && !empty($request->name)) {
            $query->where('name', 'like', $request->name);

        }
        if (isset($request->gender) && !empty($request->gender)) {
            $query->where('gender', $request->gender);
        }

        if (isset($request->country) && !empty($request->country)) {
            $query->where('country', $request->country);
        }

        if (isset($request->state) && !empty($request->state)) {
            $query->where('state', $request->state);
        }


        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('rfx-applicant-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var country = $("select[name=country]").val();
                    d.country = country

                    var state = $("select[name=state]").val();
                    d.state = state

                    var name = $("input[name=name]").val();
                    d.name = name

                     var gender = $("input[type=radio][name=gender]:checked").val();
                    d.gender = gender
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
                    if (!$("select[name=country]").val() && !$("select[name=country]").val()=="All" && !$("select[name=state]").val() && !$("select[name=state]").val()=="All" && !$("input[name=name]").val() && !$("input[name=gender]:checked").val() ) {
                        toastrs("Error!", "Please Select Atleast One Filter ", "error");
                        return;
                    }

                    $("#rfx-applicant-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("select[name=country]").val("")
                    $("select[name=state]").val("")
                    $("input[name=name]").val("")
                     $("input[name=gender]").prop("checked", false);
                    $("#rfx-applicant-table").DataTable().draw();
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
            Column::make('email')->title(__('Email')),
            Column::make('gender')->title(__('Gender')),
            Column::make('country')->title(__('Country')),
            Column::make('state')->title(__('State')),
            Column::make('city')->title(__('City')),
            Column::make('profile')->title(__('Profile'))->searchable(false)->printable(false)->exportable(false)->orderable(false),
            Column::make('proposal')->title(__('Proposal'))->searchable(false)->printable(false)->exportable(false)->orderable(false),
        ];
        if (\Laratrust::hasPermission('rfx applicant edit') || \Laratrust::hasPermission('rfx applicant delete')) {
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
        return 'Rfx_Applicant_' . date('YmdHis');
    }
}
