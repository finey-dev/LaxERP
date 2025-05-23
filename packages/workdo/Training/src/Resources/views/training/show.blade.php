@extends('layouts.main')
@section('page-title')
    {{ __('Training Details') }}
@endsection

@section('page-breadcrumb')
    {{ __('Training Details') }}
@endsection
@section('content')
    <div class="row">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive ">
                        <table class="table ">
                            <tbody>
                                <tr>
                                    <td>{{ __('Training Type') }}</td>
                                    <td class="text-right">{{ !empty($training->types) ? $training->types->name : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Trainer') }}</td>
                                    <td class="text-right">
                                        {{ !empty($training->trainers) ? $training->trainers->firstname : '--' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Training Cost') }}</td>
                                    <td class="text-right">{{ currency_format_with_sym($training->training_cost) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Start Date') }}</td>
                                    <td class="text-right">{{ company_date_formate($training->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('End Date') }}</td>
                                    <td class="text-right">{{ company_date_formate($training->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Date') }}</td>
                                    <td class="text-right">{{ company_date_formate($training->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-sm mt-4 p-2"> {{ $training->description }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6>{{ __('Training Employee') }}</h6>
                            <hr>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-0 mb-4 mt-1" style="border:0px;">
                                    <div class="d-flex align-items-center">
                                        <a href="{{ !empty($training->employees->user->avatar) ? get_file($training->employees->user->avatar) : get_file('uploads/users-avatar/avatar.png') }}"
                                            target="_blank">

                                            <img src="{{ !empty($training->employees->user->avatar) ? get_file($training->employees->user->avatar) : get_file('uploads/users-avatar/avatar.png') }}"
                                                class="rounded border-2 border border-primary" width="50px"
                                                height="50px">
                                        </a>

                                        <div class="media-body px-2 text-sm">
                                            <a href="{{ route('employee.show', !empty($training->employees) ? \Illuminate\Support\Facades\Crypt::encrypt($training->employees->user_id) : 0) }}"
                                                class="text-dark">
                                                {{ !empty($training->employees) ? $training->employees->name : '' }}

                                                {{ !empty($training->employees) ? (!empty($training->employees->designation) ? $training->employees->designation->name : '') : '' }}
                                            </a>
                                            <br>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        {{ Form::model($training, ['route' => ['training.status', $training->id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
                        <h6>{{ __('Update Status') }}</h6>
                        <hr>
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <input type="hidden" value="{{ $training->id }}" name="id">
                                <div class="form-group">
                                    {{ Form::label('performance', __('Performance'), ['class' => 'form-label text-dark']) }}
                                    {{ Form::select('performance', $performance, null, ['class' => 'form-control ', 'placeholder' => __('Select Performance')]) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('status', __('Status'), ['class' => 'form-label text-dark']) }}
                                    {{ Form::select('status', $status, null, ['class' => 'form-control ', 'placeholder' => __('Select Status')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-12">
                            <div class="form-group">
                                {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label text-dark']) }}
                                {{ Form::textarea('remarks', null, ['class' => 'form-control', 'placeholder' => __('Enter Remarks'), 'rows' => '3']) }}
                            </div>
                            @permission('training update status')
                                <div class="form-group text-end">
                                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                                </div>
                            @endpermission
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
