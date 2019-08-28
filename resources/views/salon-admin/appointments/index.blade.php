@extends('layouts.salon-backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Appointments</div>
                <div class="card-body">
                    {!! Form::open(['method' => 'GET', 'url' => '/salon-admin/appointments', 'class' => 'form-inline my-2 my-lg-0 float-right', 'role' => 'search'])  !!}
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                        <span class="input-group-append">
                            <button class="btn btn-secondary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}

                    <br/>
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th><th>Service Id</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Status</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $item)
                                <tr>
                                    <td>{{$item->id }}</td>
                                    <td>{{ $item->service_id }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->start_time }}</td>
                                    <td>{{ $item->end_time }}</td>
                                    <td>
                                        <?php if ($item->status == 'hold'): ?>
                                            <button class="btn btn-success btn-sm" title="accepted" onclick="changeStatus({{$item->id}}, 'accepted')"><i class="fa fa-check" aria-hidden="true"></i></button>
                                            <button class="btn btn-danger btn-sm" title="rejected" onclick="changeStatus({{$item->id}}, 'rejected')"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        <?php else: ?>
                                            {{$item->status}}
                                        <?php endif; ?>

                                    </td>
                                    <td>
                                        <a href="{{ url('/salon-admin/appointments/' . $item->id) }}" title="View Appointment"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $appointments->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function changeStatus(id, status) {
    Swal.fire({
    title: 'Are you sure you wanted to change status?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ' + status + ' it!'
    }).then((result) => {
    Swal.showLoading();
    if (result.value) {
    var form_data = new FormData();
    form_data.append("id", id);
    form_data.append("status", status);
    form_data.append("_token", $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
    url: "{{route('appointment.changeStatus')}}",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
//                        Swal.showLoading();
            },
            success: function (data)
            {
            Swal.fire(
                    status + ' !',
                    'Appointment has been ' + status + ' .',
                    'success'
                    ).then(() => {
            location.reload();
            });
            }
    });
    }
    });
    }
</script>
@endsection
