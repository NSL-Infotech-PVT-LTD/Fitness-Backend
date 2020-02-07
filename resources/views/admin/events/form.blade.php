<div class="form-group{{ $errors->has('name') ? 'has-error' : ''}}">
    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('description') ? 'has-error' : ''}}">
    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
    {!! Form::textarea('description', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('start_at') ? 'has-error' : ''}}">
    {!! Form::label('start_at', 'Start At', ['class' => 'control-label']) !!}
    {!! Form::input('datetime-local', 'start_at', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('start_at', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('end_at') ? 'has-error' : ''}}">
    {!! Form::label('end_at', 'End At', ['class' => 'control-label']) !!}
    {!! Form::input('datetime-local', 'end_at', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('end_at', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('location') ? 'has-error' : ''}}">
    {!! Form::label('location', 'Location', ['class' => 'control-label']) !!}
    {!! Form::text('location', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('latitude') ? 'has-error' : ''}}">
    {!! Form::label('latitude', 'Latitude', ['class' => 'control-label']) !!}
    {!! Form::text('latitude', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('latitude', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('longitude') ? 'has-error' : ''}}">
    {!! Form::label('longitude', 'Longitude', ['class' => 'control-label']) !!}
    {!! Form::text('longitude', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('longitude', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('service_id') ? 'has-error' : ''}}">
    {!! Form::label('service_id', 'Service Id', ['class' => 'control-label']) !!}
    {!! Form::number('service_id', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('service_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('organizer_id') ? 'has-error' : ''}}">
    {!! Form::label('organizer_id', 'Organizer Id', ['class' => 'control-label']) !!}
    {!! Form::number('organizer_id', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('organizer_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('guest_allowed') ? 'has-error' : ''}}">
    {!! Form::label('guest_allowed', 'Guest Allowed', ['class' => 'control-label']) !!}
    {!! Form::number('guest_allowed', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('guest_allowed', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('equipment_required') ? 'has-error' : ''}}">
    {!! Form::label('equipment_required', 'Equipment Required', ['class' => 'control-label']) !!}
    {!! Form::text('equipment_required', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('equipment_required', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
