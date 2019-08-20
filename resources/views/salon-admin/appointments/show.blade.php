@extends('layouts.salon-backend')
@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Appointment {{ $appointment->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/salon-admin/appointments') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                       
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $appointment->id }}</td>
                                    </tr>
                                    <tr><th> Service Id </th><td> {{ $appointment->service_id }} </td></tr><tr><th> Date </th><td> {{ $appointment->date }} </td></tr><tr><th> Start Time </th><td> {{ $appointment->start_time }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection