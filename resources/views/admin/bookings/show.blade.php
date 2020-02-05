@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Booking {{ $booking->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/bookings') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $booking->id }}</td>
                                </tr>
                                <tr><th> Type </th><td> {{ $booking->type }} </td></tr>
                                <tr><th> Target Name </th><td> {{ $targetId}} </td></tr>
                                <tr><th> Booked By </th><td> {{ $userId }} </td></tr>
                                <tr><th> Created By </th><td> {{ $ownerId }} </td></tr>
                                <tr><th> Rating </th><td> {{ $booking->rating }} </td></tr>
                                <tr><th> Created_at </th><td> {{ $booking->created_at }} </td></tr>





                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
