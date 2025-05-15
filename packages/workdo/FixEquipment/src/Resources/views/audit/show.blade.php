@extends('layouts.main')

@section('page-title')
    {{ __('Show Audit') }}
@endsection

@section('page-breadcrumb')
    {{ __('Audit / Show Audit') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="form-group col-md-4">
                    {{ Form::label('Audit Title', __('Audit Title'), ['class' => 'form-label']) }}
                    {{ Form::text('audit_title', $audit->audit_title, ['class' => 'form-control', 'id' => 'audit_title', 'required' => 'required', 'readonly', 'placeholder' => __('Enter Audit Title')]) }}
                </div>
                <div class="col-md-4 form-group">
                    {{ Form::label('assets', __('Assets'), ['class' => 'form-label']) }}
                    {{ Form::text('asset_title', $combinedTitles, ['class' => 'form-control', 'readonly']) }}
                </div>
                <div class="form-group col-md-4">
                    {{ Form::label('Audit Date', __('Audit Date'), ['class' => 'form-label']) }}
                    {{ Form::date('audit_date', $audit->audit_date, ['class' => 'form-control', 'id' => 'audit_date', 'readonly', 'required' => 'required']) }}
                </div>
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
                        @php
                            $dataArray = json_decode($audit->audit_data, true);
                        @endphp
                        <tbody>
                            @foreach ($dataArray as $data)
                                @if ($data['title'] !== null && $data['quantity'] !== null)
                                    <tr>
                                        <td>{{ $data['title'] }}</td>
                                        <td>{{ $data['quantity'] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
