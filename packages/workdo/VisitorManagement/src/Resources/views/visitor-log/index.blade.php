@extends('layouts.main')

@section('page-title')
    {{ __('Manage Visitor Log') }}
@endsection
@section('page-breadcrumb')
    {{ __('Visitor Log') }}
@endsection

@section('page-action')
    @permission('visitor log create')
        <div>
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Visitor Log') }}"
                data-url="{{ route('visitor-log.create') }}" data-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    @endpermission
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">

                        {{ $dataTable->table(['width' => '100%']) }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush

@push('scripts')
    <script>
        $('body').on('change', 'select[name=visitor_id]', function() {
            var id = $(this).val();
            var url = '{{ route('visitor.detail') }}';
            if (id != 0 && id != '') {
                getVisitor(url, id);
            } else {
                $('.visitor-data').removeAttr('disabled');
                $('.visitor-data').val('');
            }

        });

        function getVisitor(url, id) {
            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id
                },
                success: function(response) {
                    if (response.status === 'success') {
                        var visitor = response.data;
                        $('.visitor-data').attr('disabled', true);
                        $('input[name=first_name]').val(visitor.first_name);
                        $('input[name=last_name]').val(visitor.last_name);
                        $('input[name=email]').val(visitor.email);
                        $('input[name=phone]').val(visitor.phone);
                        $('input[name=visitor_from]').val(visitor.visitor_from);
                    }
                }
            });
        }
    </script>
@endpush
