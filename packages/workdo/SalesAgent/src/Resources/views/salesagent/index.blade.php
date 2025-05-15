@extends('layouts.main')

@section('page-title')
    {{ __('Manage Sales Agent') }}
@endsection

@section('page-breadcrumb')
    {{ __('Sales Agent') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-action')
    <div>
        @stack('addButtonHook')
        @permission('salesagent create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Sales Agent') }}"
                data-url="{{ route('salesagents.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
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
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_name']").val($("[name='billing_name']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_phone']").val($("[name='billing_phone']").val());
            $("[name='shipping_zip']").val($("[name='billing_zip']").val());
            $("[name='shipping_address']").val($("[name='billing_address']").val());
        })

        $(document).on('change', '#is_enable_login', function() {
            var val = $(this).prop("checked");
            if (val == true) {
                var is_agent_active = 1;
            } else {
                var is_agent_active = 0;
            }
            var sales_agent_id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('activeSalesAgent') }}",
                datType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "is_enable_login": is_agent_active,
                    "user_id": sales_agent_id,
                },
                success: function(data) {
                    toastrs('Success', data.message, 'success')
                }
            });
        });
    </script>
@endpush
