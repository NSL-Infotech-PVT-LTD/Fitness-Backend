<div class="form-group{{ $errors->has('name') ? 'has-error' : ''}}">
    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('images') ? 'has-error' : ''}}">
    {!! Form::label('images', 'Images', ['class' => 'control-label']) !!}
    {!! Form::textarea('images', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('images', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('description') ? 'has-error' : ''}}">
    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
    {!! Form::textarea('description', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('price_hourly') ? 'has-error' : ''}}">
    {!! Form::label('price_hourly', 'Price Hourly', ['class' => 'control-label']) !!}
    {!! Form::text('price_hourly', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('price_hourly', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('availability_week') ? 'has-error' : ''}}">
    {!! Form::label('availability_week', 'Availability Week', ['class' => 'control-label']) !!}
    {!! Form::text('availability_week', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('availability_week', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('organizer_id') ? 'has-error' : ''}}">
    {!! Form::label('organizer_id', 'Organizer Id', ['class' => 'control-label']) !!}
    {!! Form::text('organizer_id', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('organizer_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('price_weekly') ? 'has-error' : ''}}">
    {!! Form::label('price_weekly', 'Price Weekly', ['class' => 'control-label']) !!}
    {!! Form::text('price_weekly', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('price_weekly', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
