@extends('layouts.main')

@section('page-title')
    {{ __('Edit Audit') }}
@endsection

@section('page-breadcrumb')
    {{ __('Audit / Edit Audit') }}
@endsection

@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => ['fix.equipment.audit.update', $audit->id],'class'=>'needs-validation','novalidate', 'method' => 'POST']) }}
                <div class="row">
                    <div class="form-group col-md-3">
                        {{ Form::label('Audit Title', __('Audit Title'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::text('audit_title', $audit->audit_title, ['class' => 'form-control', 'id' => 'audit_title', 'required' => 'required', 'placeholder' => __('Enter Audit Title')]) }}
                    </div>
                    <div class="col-md-4 form-group">
                        {{ Form::label('assets', __('Assets'), ['class' => 'form-label']) }}<x-required></x-required>
                        <select name="asset[]" id="asset" class="form-control choices" multiple required>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}" @if (in_array($asset->id, explode(',', $audit->asset))) selected @endif>
                                    {{ $asset->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label('Audit Date', __('Audit Date'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::date('audit_date', $audit->audit_date, ['class' => 'form-control', 'id' => 'audit_date', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-1">
                        {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary mt-4 float-end']) }}
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

            function loadDataForSelectedAssets(selectedAssets) {
                $.ajax({
                    url: '{{ route('audit.get.data') }}',
                    type: 'GET',
                    data: {
                        selectedValue: selectedAssets
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
            }

            var selectedAssets = $('#asset').val();
            loadDataForSelectedAssets(selectedAssets);

            $('#asset').on('change', function() {
                var selectedValue = $(this).val();
                loadDataForSelectedAssets(selectedValue);
            });
        });
    </script>
@endpush
