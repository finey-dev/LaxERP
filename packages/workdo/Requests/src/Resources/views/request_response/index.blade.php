@extends('layouts.main')

@section('page-title')
    {{$form->name . __("'s Response") }}
@endsection

@section('page-action')
    <div>
        <a href="{{ route('requests.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Back') }}"><i class="ti ti-arrow-left text-white"></i></a>
    </div>
@endsection

@section('page-breadcrumb')
    {{ __('Request Response') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive" style="margin: -25px -25px 0 -25px;">
                        <table class="table">
                            @if ($form->response->count() > 0)
                                <tbody>
                                    @php
                                        $first = null;
                                        $second = null;
                                        $third = null;
                                        $i = 0;
                                    @endphp
                                    @foreach ($form->response as $key => $response)
                                        @php
                                            $i++;
                                            $resp = json_decode($response->response, true);
                                            if (count($resp) == 1) {
                                                $resp[''] = '';
                                                $resp[' '] = '';
                                            } elseif (count($resp) == 2) {
                                                $resp[''] = '';
                                            }
                                            $firstThreeElements = array_slice($resp, 0, 3);

                                            $thead = array_keys($firstThreeElements);
                                            $head1 = $first != $thead[0] ? $thead[0] : '';
                                            $head2 = !empty($thead[1]) && $second != $thead[1] ? $thead[1] : '';
                                            $head3 = !empty($thead[2]) && $third != $thead[2] ? $thead[2] : '';
                                        @endphp
                                        @if (!empty($head1) || !empty($head2) || (!empty($head3) && $head3 != ' '))
                                            <tr>
                                                <th>{{ $head1 }}</th>
                                                <th>{{ $head2 }}</th>
                                                <th>{{ $head3 }}</th>
                                                @if ($key == 0)
                                                    <th>{{__('Action')}}</th>
                                                @else
                                                    <th></th>
                                                @endif
                                            </tr>
                                        @endif
                                        @php
                                            $first = $thead[0];
                                            $second = $thead[1];
                                            $third = $thead[2];
                                        @endphp
                                        <tr>
                                            @foreach (array_values($firstThreeElements) as $ans)
                                                <td> {{ $ans }}</td>
                                            @endforeach
                                            <td class="Action">
                                                <span>
                                                    @permission('Requests response show')
                                                    <div class="action-btn me-2">
                                                        <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Response Detail') }}"
                                                            data-url="{{ route('requests.response.detail', $response->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('View') }}"
                                                            class="btn btn-icon btn-sm bg-warning"><i
                                                                class="ti ti-eye text-white"></i></a>
                                                    </div>
                                                    @endpermission

                                                    @permission('Requests response delete')
                                                    <div class="action-btn">
                                                        {{ Form::open(['route' => ['response-requests.destroy', $response->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a class="mx-3 btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                            aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $response->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                    @endpermission
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ __('No data available in table') }}</td>
                                    </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
      <script type="text/javascript">
        $(".filter").click(function() {
            $("#show_filter").toggleClass('display-none');
        });
    </script>
@endpush
