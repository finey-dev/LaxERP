
@push('css')
<style>
    .static-rating .voted {
        color: #ffcc00;
    }
</style>
<link rel="stylesheet" href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/main.css')}}">
@endpush

<div id="tabs-14" class="">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ __('Procurement') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('RFxs') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive">
                            <table id="" class="table  px-2">
                                <thead>
                                    <tr>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('End Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rfxs as $rfx)
                                        <tr>
                                            <td>{{ $rfx->location }}</td>
                                            <td>{{ $rfx->title }}</td>
                                            <td>{{ company_date_formate($rfx->start_date, $rfx->created_by, $rfx->workspace) }}
                                            </td>
                                            <td>{{ company_date_formate($rfx->end_date, $rfx->created_by, $rfx->workspace) }}
                                            </td>
                                            <td>
                                                @if ($rfx->status == 'active')
                                                    <span
                                                        class="badge bg-success p-2 px-3 rounded status-badge">{{ Workdo\Procurement\Entities\Rfx::$status[$rfx->status] }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger p-2 px-3 rounded status-badge">{{ Workdo\Procurement\Entities\Rfx::$status[$rfx->status] }}</span>
                                                @endif
                                            </td>
                                            <td class="Action">
                                                @if (Laratrust::hasPermission('rfx edit'))
                                                    <span>
                                                        @permission('rfx edit')
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="{{ route('rfx.edit', $rfx->id) }}"
                                                                    class="mx-3 btn btn-sm  align-items-center"
                                                                    data-url="" data-title="{{ __('Edit RFx') }}"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        @include('layouts.nodatafound')
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('RFx Application') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive">
                            <table id="" class="table  px-2">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Stage') }}</th>
                                        <th>{{ __('Proposal') }}</th>
                                        <th>{{ __('Rating') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stages as $key => $stage)
                                        @php
                                            $applications = $stage->applicationsProject(
                                                $filter,
                                                $stage->created_by,
                                                $stage->workspace,
                                            );
                                        @endphp
                                        @foreach ($applications as $application)
                                            <tr>
                                                <td>
                                                    {{ !empty($application->name) ? $application->name : '' }}
                                                </td>
                                                <td>{{ !empty($application->stages) ? $application->stages->title : '' }}
                                                </td>
                                                <td>
                                                    @if (!empty($application->proposal))
                                                        <span class="text-sm action-btn bg-primary ms-2">
                                                            <a class=" btn btn-sm align-items-center"
                                                                href="{{ get_file($application->proposal, $application->created_by, $application->workspace) }}"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('download') }}"
                                                                download=""><i
                                                                    class="ti ti-download text-white"></i></a>
                                                        </span>

                                                        <div class="action-btn bg-secondary ms-2 ">
                                                            <a class=" mx-3 btn btn-sm align-items-center"
                                                                href="{{ get_file($application->proposal) }}"
                                                                target="_blank">
                                                                <i class="ti ti-crosshair text-white"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('Preview') }}"></i>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="mx-4">-</div>
                                                    @endif
                                                </td>
                                                <td><span class="static-rating static-rating-sm d-block">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $application->rating)
                                                                <i class="star fas fa-star voted"></i>
                                                            @else
                                                                <i class="star fas fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </span></td>
                                                <td>{{ company_date_formate($application->created_at, $rfx->created_by, $rfx->workspace) }}
                                                <td class="Action">
                                                    @if (Laratrust::hasPermission('rfxapplication delete') || Laratrust::hasPermission('rfxapplication show'))
                                                        <span>

                                                            @permission('rfxapplication show')
                                                                <div class="action-btn bg-warning ms-2">
                                                                    <a href="{{ route('rfx-application.show', \Crypt::encrypt($application->id)) }}"
                                                                        class="mx-3 btn btn-sm  align-items-center"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="{{ __('rfx application Detail') }}">
                                                                        <i class="ti ti-eye text-white"></i>
                                                                    </a>
                                                                </div>
                                                            @endpermission
                                                            @permission('rfxapplication delete')
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['rfx-application.destroy', $application->id],
                                                                        'id' => 'delete-form-' . $application->id,
                                                                    ]) !!}
                                                                    <a href="#!"
                                                                        class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                                        title="{{ __('Delete') }}">
                                                                        <i class="ti ti-trash text-white"></i></a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endpermission
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('RFx Applicant') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive">
                            <table id="" class="table  px-2">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Gender') }}</th>
                                        <th>{{ __('Country') }}</th>
                                        <th>{{ __('State') }}</th>
                                        <th>{{ __('City') }}</th>
                                        <th>{{ __('Proposal Status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rfxApplicants as $rfxApp)
                                        <tr>
                                            <td>{{ $rfxApp->name }}</td>
                                            <td>{{ $rfxApp->gender }}</td>
                                            <td>{{ $rfxApp->country }}</td>
                                            <td>{{ $rfxApp->state }}</td>
                                            <td>{{ $rfxApp->city }}</td>
                                            <td>
                                                @if (!empty($rfxApp->proposal))
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a class="mx-3 btn btn-sm align-items-center"
                                                            href="{{ get_file($rfxApp->proposal) }}" download>
                                                            <i class="ti ti-download text-white"></i>
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-secondary ms-2">
                                                        <a class="mx-3 btn btn-sm align-items-center"
                                                            href="{{ get_file($rfxApp->proposal) }}" target="_blank">
                                                            <i class="ti ti-crosshair text-white"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Preview') }}"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <p>-</p>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        @include('layouts.nodatafound')
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('Vendor On Boarding') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive">
                            <table id="" class="table  px-2">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile No') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Applied at') }}</th>
                                        <th>{{ __('Joining At') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vendorOnBoards as $vendor)
                                        <tr>
                                            <td>{{ !empty($vendor->applications) ? $vendor->applications->name : '-' }}
                                            </td>
                                            <td>{{ !empty($vendor->applications) ? $vendor->applications->email : '-' }}
                                            </td>
                                            <td>{{ !empty($vendor->applications) ? $vendor->applications->phone : '-' }}
                                            </td>
                                            <td>{{ !empty($vendor->applications) ? (!empty($vendor->applications->rfx) ? $vendor->applications->rfxs->location : '-') : '-' }}
                                            </td>
                                            <td>{{ company_date_formate(!empty($vendor->applications) ? $vendor->applications->created_at : '-', $vendor->created_by, $vendor->workspace) }}
                                            </td>
                                            <td>{{ company_date_formate($vendor->joining_date, $vendor->created_by, $vendor->workspace) }}
                                            </td>
                                            <td>
                                                @if ($vendor->status == 'pending')
                                                    <span
                                                        class="badge bg-warning p-2 px-3 rounded">{{ Workdo\Procurement\Entities\VendorOnBoard::$status[$vendor->status] }}</span>
                                                @elseif($vendor->status == 'cancel')
                                                    <span
                                                        class="badge bg-danger p-2 px-3 rounded">{{ Workdo\Procurement\Entities\VendorOnBoard::$status[$vendor->status] }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-success p-2 px-3 rounded">{{ Workdo\Procurement\Entities\VendorOnBoard::$status[$vendor->status] }}</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        @include('layouts.nodatafound')
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('InterViews') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive">
                            <div class="card-body">
                                <div id='calendar' class='calendar'></div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/main.min.js') }}"></script>
    <script type="text/javascript">
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
                },
                themeSystem: 'bootstrap',
                slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                events: {!! $arrSchedule !!},
            });
            calendar.render();
        })();
    </script>
    <script>
        $(document).on('click', '.fc-daygrid-event', function(e) {
            e.preventDefault();
            var event = $(this);
            var title = $(this).find('.fc-event-title').html();
            var size = 'md';
            var url = $(this).attr('href');
            $("#commonModal .modal-title ").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            $.ajax({
                url: url,
                success: function(data) {
                    $('#commonModal .body').html(data);
                    $("#commonModal").modal('show');
                    if ($(".d_week").length > 0) {
                        $($(".d_week")).each(function(index, element) {
                            var id = $(element).attr('id');
    
                            (function() {
                                const d_week = new Datepicker(document.querySelector('#' +
                                    id), {
                                    buttonClass: 'btn',
                                    format: 'yyyy-mm-dd',
                                });
                            })();
    
                        });
                    }
    
                },
                error: function(data) {
                    data = data.responseJSON;
                    toastrs('Error', data.error, 'error')
                }
            });
        });
    </script>
    @endpush
