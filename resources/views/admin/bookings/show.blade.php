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
                                <tr><th> Target Name </th> 
                                    <?php
                                    if ($booking->type == 'event') {
                                        ?>
                                        <td>{{$targetIdEvent}}</td>
                                    <?php } elseif ($booking->type == 'session') {
                                        ?>
                                        <td>{{$targetIdSession}}</td>
                                        <?php
                                    } else {
                                        ?>
                                        <td>{{$targetIdSpace}}</td>
                                    <?php }
                                    ?>
                                </tr>
                                <tr><th> Booked By </th><td> {{ $userId }} </td></tr>
                                <tr><th> Created By </th><td>{{$ownerId}}</td></tr>
                                <tr><th> Rating </th><td> {{ $booking->rating }} </td></tr>
                                <tr><th> Booked At </th><td> {{ $booking->created_at }} </td></tr>
                                <?php
                                if ($booking->type == 'event') {
                                    ?>
                                    <tr><th> Seats Booked </th><td> {{ $booking->tickets }} </td></tr>
                                    <tr><th> Price </th><td> {{ $price }} </td></tr>
                                <?php }
                                ?>
                                <?php
                                if ($booking->type == 'space') {
                                    ?>
                                    <tr><th> Booked Date </th><td> {{ $spacedate }} </td></tr>
                                    <tr><th> Booked From </th><td> {{ $spacetimefrom }} </td></tr>
                                    <tr><th> Booked To </th><td> {{ $spacetimeto }} </td></tr>
                                    <tr><th> Booking price hourly </th><td> {{ $spaceprice }} </td></tr>
                                <?php }
                                ?>




                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
