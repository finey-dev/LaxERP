@extends('layouts.main')
@section('page-title')
    {{ __('Manage Quote') }}
@endsection
@section('title')
    {{ __('Quote') }}
@endsection
@section('page-breadcrumb')
    {{ __('Quote') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('quote.grid') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @permission('quote create')
            <a data-url="{{ route('quote.create', ['quote', 0]) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"
                data-title="{{ __('Create Quote') }}" title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        @include('layouts.includes.datatable-js')
        {{ $dataTable->scripts() }}
        <script>
            $(document).on('click', '#billing_data', function() {
                $("[name='shipping_address']").val($("[name='billing_address']").val());
                $("[name='shipping_city']").val($("[name='billing_city']").val());
                $("[name='shipping_state']").val($("[name='billing_state']").val());
                $("[name='shipping_country']").val($("[name='billing_country']").val());
                $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
            })

            $(document).on('change', 'select[name=opportunity]', function() {

                var opportunities = $(this).val();
                getaccount(opportunities);
            });

            function getaccount(opportunities_id) {
                $.ajax({
                    url: '{{ route('quote.getaccount') }}',
                    type: 'POST',
                    data: {
                        "opportunities_id": opportunities_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        $('#amount').val(data.opportunitie.amount);
                        $('#account_name').val(data.account.name);
                        $('#account_id').val(data.account.id);
                        $('#billing_address').val(data.account.billing_address);
                        $('#shipping_address').val(data.account.shipping_address);
                        $('#billing_city').val(data.account.billing_city);
                        $('#billing_state').val(data.account.billing_state);
                        $('#shipping_city').val(data.account.shipping_city);
                        $('#shipping_state').val(data.account.shipping_state);
                        $('#billing_country').val(data.account.billing_country);
                        $('#billing_postalcode').val(data.account.billing_postalcode);
                        $('#shipping_country').val(data.account.shipping_country);
                        $('#shipping_postalcode').val(data.account.shipping_postalcode);

                    }
                });
            }
        </script>
    @endpush
