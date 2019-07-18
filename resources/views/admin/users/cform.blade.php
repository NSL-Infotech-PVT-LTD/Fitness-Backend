<div class="form-group{{ $errors->has('firstname') ? ' has-error' : ''}}">
    {!! Form::label('firstname', 'Name: ', ['class' => 'control-label']) !!}
    {!! Form::text('firstname', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('firstname', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('email') ? ' has-error' : ''}}">
    {!! Form::label('email', 'Email: ', ['class' => 'control-label']) !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('	phone') ? ' has-error' : ''}}">
    {!! Form::label('phone', 'Phone: ', ['class' => 'control-label']) !!}
    {!! Form::text('phone', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('experience') ? ' has-error' : ''}}">
    {!! Form::label('experience', 'Experience: ', ['class' => 'control-label']) !!}
    {!! Form::text('experience', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('experience', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('hourly_rate') ? ' has-error' : ''}}">
    {!! Form::label('hourly_rate', 'Hourly Rate: ', ['class' => 'control-label']) !!}
    {!! Form::text('hourly_rate', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('hourly_rate', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('latitude') ? ' has-error' : ''}}">
    {!! Form::label('latitude', 'Latitude: ', ['class' => 'control-label']) !!}
    {!! Form::text('latitude', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('latitude', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('longitude') ? ' has-error' : ''}}">
    {!! Form::label('longitude', 'Longitude: ', ['class' => 'control-label']) !!}
    {!! Form::text('longitude', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('longitude', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('profile_pic') ? ' has-error' : ''}}">
    {!! Form::label('profile_pic', 'Profile Picture: ', ['class' => 'control-label']) !!}
    {!! Form::file('profile_pic', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('profile_pic', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group{{ $errors->has('roles') ? ' has-error' : ''}}">
    {!! Form::label('role', 'Role: ', ['class' => 'control-label']) !!}
    {!! Form::select('roles[]', $roles, isset($user_roles) ? $user_roles : [], ['class' => 'form-control', 'multiple' => true]) !!}
</div>
<div class="form-group{{ $errors->has('portfolio_image') ? ' has-error' : ''}}">
    {!! Form::label('portfolio_image', 'Portfolio Image: ', ['class' => 'control-label']) !!}
    {!! Form::file('portfolio_image', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('portfolio_image', '<p class="help-block">:message</p>') !!}
</div>
 <div class="form-group{{ $errors->has('bio') ? ' has-error' : ''}}">
    {!! Form::label('bio', 'Biography: ', ['class' => 'control-label']) !!}
    {!! Form::textarea('bio', null, ['class' => 'form-control', 'required' => 'required'])!!}
</div>
<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
