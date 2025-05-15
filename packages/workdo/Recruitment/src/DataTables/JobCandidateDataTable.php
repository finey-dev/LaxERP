<?php

namespace Workdo\Recruitment\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\Recruitment\Entities\JobCandidate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobCandidateDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['name', 'candidate_category', 'email', 'gender', 'country', 'state', 'city', 'profile', 'resume', 'show_resume'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (JobCandidate $job_candidates) {
                return $job_candidates->name ?? '-';
            })
            ->editColumn('candidate_category', function (JobCandidate $job_candidates) {
                return $job_candidates->candidate_category ? $job_candidates->CandidateCategory->name ?? '-' : '-';
            })
            ->editColumn('email', function (JobCandidate $job_candidates) {
                return $job_candidates->email ?? '-';
            })
            ->editColumn('gender', function (JobCandidate $job_candidates) {
                return $job_candidates->gender ?? '-';
            })
            ->editColumn('country', function (JobCandidate $job_candidates) {
                return $job_candidates->country ?? '-';
            })
            ->editColumn('state', function (JobCandidate $job_candidates) {
                return $job_candidates->state ?? '-';
            })
            ->editColumn('city', function (JobCandidate $job_candidates) {
                return $job_candidates->city ?? '-';
            })
            ->editColumn('profile', function (JobCandidate $job_candidates) {
                if (!empty($job_candidates->profile)) {
                    $html = '<span class="text-sm action-btn  me-2">';
                    $html .= '<a class="btn btn-sm align-items-center bg-primary" href="' . get_file($job_candidates->profile) . '"';
                    $html .= ' data-bs-toggle="tooltip" data-bs-original-title="' . __('Download') . '" download>';
                    $html .= '<i class="ti ti-download text-white"></i></a>';
                    $html .= '</span>';

                    $html .= '<div class="action-btn ">';
                    $html .= '<a class="mx-3 btn btn-sm align-items-center bg-secondary" href="' . get_file($job_candidates->profile) . '" target="_blank">';
                    $html .= '<i class="ti ti-crosshair text-white" data-bs-toggle="tooltip" data-bs-original-title="' . __('Preview') . '"></i>';
                    $html .= '</a></div>';
                } else {
                    $html = '-';
                }

                return $html;
            })
            ->editColumn('resume', function (JobCandidate $job_candidates) {
                if (!empty($job_candidates->resume)) {
                    $html = '<span class="text-sm action-btn me-2">';
                    $html .= '<a class="btn btn-sm align-items-center bg-primary" href="' . get_file($job_candidates->resume) . '"';
                    $html .= ' data-bs-toggle="tooltip" data-bs-original-title="' . __('Download') . '" download>';
                    $html .= '<i class="ti ti-download text-white"></i></a>';
                    $html .= '</span>';

                    $html .= '<div class="action-btn ">';
                    $html .= '<a class="mx-3 btn btn-sm align-items-center bg-secondary" href="' . get_file($job_candidates->resume) . '" target="_blank">';
                    $html .= '<i class="ti ti-crosshair text-white" data-bs-toggle="tooltip" data-bs-original-title="' . __('Preview') . '"></i>';
                    $html .= '</a></div>';
                } else {
                    $html = '-';
                }

                return $html;
            })
            ->addColumn('show_resume', function (JobCandidate $job_candidates) {
                $url = route('resume.pdf', Crypt::encrypt($job_candidates->id));
                $html = '<div class="action-btn">';
                $html .= '<a class="mx-3 btn btn-sm align-items-center bg-warning" href="' . $url . '" data-bs-toggle="tooltip" data-bs-placement="top" data-title="' . __('View') . '" title="' . __('View') . '" target="_blank">';
                $html .= '<i class="ti ti-eye text-white"></i>';
                $html .= '</a></div>';

                return $html;
            })
            ->filterColumn('candidate_category', function ($query, $keyword) {
                $query->whereHas('CandidateCategory', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            });
        if (\Laratrust::hasPermission('job candidate edit') || \Laratrust::hasPermission('job candidate delete')) {
            $dataTable->addColumn('action', function (JobCandidate $job_candidates) {
                return view('recruitment::jobcandidate.button', compact('job_candidates'));
            });
            $rowColumn[] = 'action';
        }
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobCandidate $model, Request $request): QueryBuilder
    {
        $job_candidates_query = $model->where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace());

        if (isset($request->gender) && !empty($request->gender)) {
            $job_candidates_query->where('gender', $request->gender);
        }

        if (isset($request->candidate_category) && !empty($request->candidate_category)) {

            $job_candidates_query->where('candidate_category', $request->candidate_category);
        }

        if (isset($request->country) && !empty($request->country)) {
            $job_candidates_query->where('country', $request->country);
        }

        if (isset($request->state) && !empty($request->state)) {
            $job_candidates_query->where('state', $request->state);
        }

        $job_candidates = $job_candidates_query->with('CandidateCategory');

        return $job_candidates;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('job-candidate-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    var gender = $("input[name=gender]:radio:checked").val();
                    d.gender = gender

                    var candidate_category = $("select[name=candidate_category]").val();
                    d.candidate_category = candidate_category

                    var country = $("select[name=country]").val();
                    d.country = country

                    var state = $("select[name=state]").val();
                    d.state = state
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

                    if (!$("input[name=gender]:radio:checked").val() && !$("select[name=candidate_category]").val() && !$("select[name=country]").val() && !$("select[name=state]").val()) {
                        toastrs("Error!", "Please select Atleast One Filter ", "error");
                        return;
                    }

                    $("#job-candidate-table").DataTable().draw();
                });

                $("body").on("click", "#clearfilter", function() {
                    $("input[name=gender]:radio:checked").val("")
                    $("select[name=candidate_category]").val("")
                    $("select[name=country]").val("")
                    $("select[name=state]").val("")
                    $("#job-candidate-table").DataTable().draw();
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
            Column::make('id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('name')->title(__('Name')),
            Column::make('candidate_category')->title(__('Category')),
            Column::make('email')->title(__('Email')),
            Column::make('gender')->title(__('Gender')),
            Column::make('country')->title(__('Country')),
            Column::make('state')->title(__('State')),
            Column::make('city')->title(__('City')),
            Column::make('profile')->title(__('Profile'))->searchable(false)->exportable(false)->printable(false),
            Column::make('resume')->title(__('Resume'))->searchable(false)->exportable(false)->printable(false),
            Column::computed('show_resume')->title(__('Show Resume'))->searchable(false)->exportable(false)->printable(false),
        ];
        if (
            \Laratrust::hasPermission('job candidate edit') ||
            \Laratrust::hasPermission('job candidate delete')
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
        return 'Job Candidates_' . date('YmdHis');
    }
}
