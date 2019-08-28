<div class="form-group{{ $errors->has('customer_id') ? 'has-error' : ''}}">
    {!! Form::label('customer_id', 'Customer Id', ['class' => 'control-label']) !!}
    {!! Form::number('customer_id', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('customer_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('payment') ? 'has-error' : ''}}">
    {!! Form::label('payment', 'Payment', ['class' => 'control-label']) !!}
    {!! Form::number('payment', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('payment', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('discounts') ? 'has-error' : ''}}">
    {!! Form::label('discounts', 'Discounts', ['class' => 'control-label']) !!}
    {!! Form::number('discounts', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('discounts', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('tax') ? 'has-error' : ''}}">
    {!! Form::label('tax', 'Tax', ['class' => 'control-label']) !!}
    {!! Form::number('tax', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('tax', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('total') ? 'has-error' : ''}}">
    {!! Form::label('total', 'Total', ['class' => 'control-label']) !!}
    {!! Form::number('total', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('total', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('total_paid') ? 'has-error' : ''}}">
    {!! Form::label('total_paid', 'Total Paid', ['class' => 'control-label']) !!}
    {!! Form::number('total_paid', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('total_paid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('invoice') ? 'has-error' : ''}}">
    {!! Form::label('invoice', 'Invoice', ['class' => 'control-label']) !!}
    {!! Form::number('invoice', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('invoice', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('status') ? 'has-error' : ''}}">
    {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
    {!! Form::text('status', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
