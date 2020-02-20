<div class="form-group{{ $errors->has('about_us') ? 'has-error' : ''}}">
    {!! Form::label('about_us', 'About Us', ['class' => 'control-label']) !!}
    {!! Form::textarea('about_us', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('about_us', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('terms_and_conditions_organiser') ? 'has-error' : ''}}">
    {!! Form::label('terms_and_conditions_organiser', 'Terms And Conditions Organiser', ['class' => 'control-label']) !!}
    {!! Form::textarea('terms_and_conditions_organiser', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('terms_and_conditions_organiser', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('terms_and_conditions_coach') ? 'has-error' : ''}}">
    {!! Form::label('terms_and_conditions_coach', 'Terms And Conditions Coach', ['class' => 'control-label']) !!}
    {!! Form::textarea('terms_and_conditions_coach', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('terms_and_conditions_coach', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('terms_and_conditions_athlete') ? 'has-error' : ''}}">
    {!! Form::label('terms_and_conditions_athlete', 'Terms And Conditions Athlete', ['class' => 'control-label']) !!}
    {!! Form::textarea('terms_and_conditions_athlete', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('terms_and_conditions_athlete', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
