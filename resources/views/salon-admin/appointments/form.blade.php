<?php
$services = [];
foreach (App\Service::get() as $service):
    $services[$service->id] = $service->name;
endforeach;
?>
<div class="form-group{{ $errors->has('service_id') ? 'has-error' : ''}}">
    {!! Form::label('service_id', 'Service Id', ['class' => 'control-label']) !!}
    {!! Form::select('service_id', $services,(isset($appointment->service_id)?$appointment->service_id:''), ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('service_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('date') ? 'has-error' : ''}}">
    {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
    {!! Form::date('date', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('start_time') ? 'has-error' : ''}}">
    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label']) !!}
    {!! Form::input('time', 'start_time', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('start_time', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('end_time') ? 'has-error' : ''}}">
    {!! Form::label('end_time', 'End Time', ['class' => 'control-label']) !!}
    {!! Form::input('time', 'end_time', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('end_time', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('comments') ? 'has-error' : ''}}">
    {!! Form::label('comments', 'Comments', ['class' => 'control-label']) !!}
    {!! Form::textarea('comments', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('comments', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
