@extends('layouts.main')

@section('page-title')
    {{ __('Create Audit') }}
@endsection

@section('page-breadcrumb')
    {{ __('Audit / Create Audit') }}
@endsection

@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => 'fix.equipment.audit.store', 'class'=>'needs-validation','novalidate','method' => 'POST']) }}
                <div class="row">
                    <div class="form-group col-md-3">
                        {{ Form::label('Audit Title', __('Audit Title'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::text('audit_title', null, ['class' => 'form-control', 'id' => 'audit_title', 'required' => 'required', 'placeholder' => __('Enter Audit Title')]) }}
                    </div>
                    <div class="col-md-4 form-group">
                        {{ Form::label('assets', __('Assets'), ['class' => 'form-label']) }}<x-required></x-required>
                        <select name="asset[]" id="asset" class="form-control choices" multiple required>
                            <option value="">{{ __('Select Asset') }}</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label('Audit Date', __('Audit Date'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::date('audit_date', now()->toDateString(), ['class' => 'form-control', 'id' => 'audit_date', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-1">
                        {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary mt-4 float-end']) }}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card ">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 pc-dt-simple datatable" id="datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            $('#asset').on('change', function() {
                var selectedValue = $(this).val();
                $.ajax({
                    url: '{{ route('audit.get.data') }}',
                    type: 'GET',
                    data: {
                        selectedValue: selectedValue
                    },
                    success: function(data) {
                        if (data.data && data.data.length > 0) {
                            $('#datatable tbody').empty();
                            $.each(data.data, function(index, item) {
                                if (item.title != null && item.quantity != null) {
                                    var newRow = '<tr><td>' + item.title + '</td><td>' +
                                        item.quantity + '</td></tr>';
                                    $('#datatable tbody').append(newRow);
                                }
                            });
                        } else {
                            $('#datatable tbody').empty();
                            $('#datatable tbody').append(
                                '<tr><td colspan="2">No data available</td></tr>');
                        }
                    }
                });
            });
        });
    </script>
@endpush
