<div class="form-group{{ $errors->has('name') ? 'has-error' : ''}}">
    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<!--<div class="form-group{{ $errors->has('images_1') ? 'has-error' : ''}}">
    {!! Form::label('images_1', 'Images 1', ['class' => 'control-label']) !!}
    {!! Form::textarea('images_1', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('images_1', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('description') ? 'has-error' : ''}}">
    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
    {!! Form::textarea('description', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('price_hourly') ? 'has-error' : ''}}">
    {!! Form::label('price_hourly', 'Price Hourly', ['class' => 'control-label']) !!}
    {!! Form::text('price_hourly', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('price_hourly', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('availability_week') ? 'has-error' : ''}}">
    {!! Form::label('availability_week', 'Availability Week', ['class' => 'control-label']) !!}
    {!! Form::text('availability_week', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('availability_week', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('open_hours_from') ? 'has-error' : ''}}">
    {!! Form::label('open_hours_from', 'Open Hours From', ['class' => 'control-label']) !!}
    {!! Form::text('open_hours_from', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('open_hours_from', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('open_hours_to') ? 'has-error' : ''}}">
    {!! Form::label('open_hours_to', 'Open Hours To', ['class' => 'control-label']) !!}
    {!! Form::text('open_hours_to', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('open_hours_to', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('price_daily') ? 'has-error' : ''}}">
    {!! Form::label('price_daily', 'Price Daily', ['class' => 'control-label']) !!}
    {!! Form::text('price_daily', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('price_daily', '<p class="help-block">:message</p>') !!}
</div>-->


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
