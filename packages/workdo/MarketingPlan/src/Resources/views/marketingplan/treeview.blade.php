@extends('layouts.main')
@section('page-title')
    {{ __('Manage Marketing Plan') }}
@endsection
@section('page-breadcrumb')
    {{ __('Marketing Plan') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('marketing-plan.grid') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="text-white ti ti-layout-grid"></i>
        </a>

        <a href="{{ route('marketing-plan.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="text-white ti ti-list"></i>
        </a>

        <a href="{{ route('marketing-plan.kanban') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Kanban View') }}"
            class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-table"></i>
        </a>

        @permission('marketing plan create')
            <a href="{{ route('marketing-plan.create',[0]) }}" data-title="{{ __('Create New Marking Plan') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-end">
                    <div class="col-xl-10">
                        <div class="row">
                            <div class="col-9">
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('challenges', __('Challenges'), ['class' => 'form-label']) }}
                                    {{ Form::select('challenges', $Challenges_name, $_GET['challenges'] ?? '', ['class' => 'form-control ', 'id' => 'challengesSelect']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tree">
            <ul class="responsive-tree">
                <li>
                    <a href="#" class="challengeName"></a>
                    <ul class="tree-challenges">
                    </ul>
                </li>
            </ul>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/jquery-3.2.1.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#challengesSelect').trigger('change');
        });
        $(document).on('change', 'select[name=challenges]', function() {
            var challeng_id = $('#challengesSelect').val();
            getTreeView(challeng_id);
        });

        function getTreeView(bid) {
            $.ajax({
                url: '{{ route('marketing-plan.gettreeview') }}',
                type: 'POST',
                data: {
                    "challeng_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('.tree-challenges').empty();
                    $('.challengeName').html(data.Challenges_name);

                    $.each(data.creatvity_name, function(key, value) {
                        $('.tree-challenges').append('<li><a href="#">' + value + '</a></li>');
                    });
                }
            });
        }
    </script>
@endpush
