@extends('layouts.salon-backend')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Edit Appointment #{{ $appointment->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/salon-admin/appointments') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        {!! Form::model($appointment, [
                            'method' => 'PATCH',
                            'url' => ['/salon-admin/appointments', $appointment->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('salon-admin.appointments.form', ['formMode' => 'edit'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
