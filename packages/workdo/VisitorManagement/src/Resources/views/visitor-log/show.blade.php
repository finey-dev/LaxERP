{{ Form::model($visitorLog,['route'=>['visitorlog.departuretime',$visitorLog->id],'class'=>'needs-validation','novalidate','method' =>'POST']) }}

<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered ">
            <tr>
                <th>{{ __('First Name ') }}</th>
                <td>{{ !empty($visitorLog->visitor) ? $visitorLog->visitor->first_name : ''}}</td>
            </tr>
            <tr>
                <th>{{ __('Last Name') }}</th>
                <td>{{ !empty($visitorLog->visitor) ? $visitorLog->visitor->last_name : '' }}</td>
            </tr>
            <tr>
                <th>{{ __('Email') }}</th>
                <td>{{ !empty($visitorLog->visitor) ? $visitorLog->visitor->email : ''  }}</td>
            </tr>
            <tr>
                <th>{{ __('Phone') }}</th>
                <td>{{ !empty($visitorLog->visitor) ? $visitorLog->visitor->phone : '' }}</td>
            </tr>
            <tr>
                <th>{{ __('Visit Purpose') }}</th>
                <td>{{ $visitorLog->getVisitReasonName() }}</td>
            </tr>
            <tr>
                <th>{{ __('Visitor Arrival Time') }}</th>
                <td>{{ $visitorLog->check_in }}</td>
            </tr> 
            <tr>
                <th>{{ __('Visitor Departure Time') }}</th>
                <td>{{ Form::datetimeLocal('check_out', null, ['class'=>'form-control','placeholder'=> __('Select Date/Time'),'required'=>'required']) }}</td>
            </tr> 
        </table>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
