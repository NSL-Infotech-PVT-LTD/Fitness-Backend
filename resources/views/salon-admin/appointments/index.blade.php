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
                                    <td>{{ $item->status }}</td>
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
@endsection
