<div class="form-group{{ $errors->has('name') ? ' has-error' : ''}}">
    {!! Form::label('name', 'Name: ', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : ''}}">
    {!! Form::label('email', 'Email: ', ['class' => 'control-label']) !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('password') ? ' has-error' : ''}}">
    {!! Form::label('password', 'Password: ', ['class' => 'control-label']) !!}
    @php
        $passwordOptions = ['class' => 'form-control'];
        if ($formMode === 'create') {
            $passwordOptions = array_merge($passwordOptions, ['required' => 'required']);
        }
    @endphp
    {!! Form::password('password', $passwordOptions) !!}
    {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
</div>


<!--div class="form-group{{ $errors->has('experience') ? ' has-error' : ''}}">
    {--!! Form::label('experience', 'Experience: ', ['class' => 'control-label']) !!--}
      {--!! Form::text('experience', null, ['class' => 'form-control', 'required' => 'required']) !!--}
</div-->
<!--div class="form-group{{-- $errors->has('hourly_rate') ? ' has-error' : ''--}}">
    {--!! Form::label('hourly_rate', 'Hourly rate: ', ['class' => 'control-label']) !!--}
      {--!! Form::text('hourly_rate', null, ['class' => 'form-control', 'required' => 'required']) !!--}
</div-->
<!--div class="form-group{{-- $errors->has('latitude') ? ' has-error' : ''--}}">
    {--!! Form::label('latitude', 'Latitude: ', ['class' => 'control-label']) !!--}
      {--!! Form::text('latitude', null, ['class' => 'form-control', 'required' => 'required']) !!--}
</div-->
<!--div class="form-group{{-- $errors->has('longitude') ? ' has-error' : ''--}}">
    {--!! Form::label('longitude', 'Longitude: ', ['class' => 'control-label']) !!}
      {--!! Form::text('longitude', null, ['class' => 'form-control', 'required' => 'required']) !!--}
</div-->
<!--div class="form-group{{-- $errors->has('image') ? ' has-error' : ''--}}">
    {--!! Form::label('image', 'Image: ', ['class' => 'control-label']) !!--}
    {{--Form::file('image')--}}
</div-->
<div class="form-group{{ $errors->has('roles') ? ' has-error' : ''}}">
    {!! Form::label('role', 'Role: ', ['class' => 'control-label']) !!}
    {!! Form::select('roles[]', $roles, isset($user_roles) ? $user_roles : [], ['class' => 'form-control', 'multiple' => true]) !!}
</div>
 <!--div class="form-group{{ $errors->has('biography') ? ' has-error' : ''}}">
    {--!! Form::label('biography', 'Biography: ', ['class' => 'control-label']) !!--}
    {--!! Form::textarea('biography', null, ['class' => 'form-control', 'required' => 'required'])!!--}
</div-->
<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
