<?php
$roleusers = \DB::table('role_user')->where('role_id', '2')->pluck('user_id');
$users = [];
$users[] = 'Please select Users';
foreach (App\User::wherein('id', $roleusers)->get() as $user):
    $users[$user->id] = $user->full_name;
endforeach;
$services = [];
//foreach (App\Service::get() as $service):
//    $services[$service->id] = $service->name;
//endforeach;
?>
<div class="form-group{{ $errors->has('salon_user_id') ? 'has-error' : ''}}">
    {!! Form::label('salon_user_id', 'Salon User', ['class' => 'control-label']) !!}
    {!! Form::select('salon_user_id', $users,(isset($appointment->salon_user_id)?$appointment->salon_user_id:''), ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('salon_user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="hidden form-group{{ $errors->has('service_id') ? 'has-error' : ''}}">
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
<script type="text/javascript">
    $(function () {
        $('#salon_user_id').on('change', function () {
//            console.log($(this).val());
            var id = $(this).val();
            if (id == 0) {
                return false;
            }
            swal.showLoading();
            var form_data = new FormData();
            form_data.append("id", id);
            form_data.append("_token", $('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: "{{route('appointment.getservice')}}",
                method: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                },
                success: function (data)
                {
//                    console.log(data.html);
                    if (data.html !== "") {
                        var serviceId = $('#service_id');
                        serviceId.html(data.html);
                        serviceId.parent().removeClass('hidden');
                        swal.close();
                    } else {
                        Swal.fire(
                                'No Service Found !',
                                'Please choose another salon which have services .',
                                'error'
                                );
                    }
                }
            });
        });
    });
</script>