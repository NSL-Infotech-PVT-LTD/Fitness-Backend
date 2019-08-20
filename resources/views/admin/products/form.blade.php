<!--<div class="form-group{{ $errors->has('id') ? 'has-error' : ''}}">
    {!! Form::label('id', 'Id', ['class' => 'control-label']) !!}
    {!! Form::number('id', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('id', '<p class="help-block">:message</p>') !!}
</div>-->
<?php
foreach (['name' => 'text', 'short_description' => 'textarea', 'description' => 'textarea', 'directions_for_use' => 'textarea', 'quantity' => 'number', 'price' => 'number', 'image' => 'file'] as $input => $type):
    ?>

    <div class="form-group{{ $errors->has($input) ? 'has-error' : ''}}">
        {!! Form::label($input, str_replace('_',' ',$input), ['class' => 'control-label']) !!}
        {!! Form::$type($input, null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
        {!! $errors->first($input, '<p class="help-block">:message</p>') !!}
    </div>
<?php endforeach; ?>

<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
