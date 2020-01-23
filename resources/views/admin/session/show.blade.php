@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Session {{ $session->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/session') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/admin/session/' . $session->id . '/edit') }}" title="Edit Session"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                    {!! Form::open([
                    'method'=>'DELETE',
                    'url' => ['admin/session', $session->id],
                    'style' => 'display:inline'
                    ]) !!}
                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                    'type' => 'submit',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => 'Delete Session',
                    'onclick'=>'return confirm("Confirm delete?")'
                    ))!!}
                    {!! Form::close() !!}
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $session->id }}</td>
                                </tr>
                                <tr><th> Name </th><td> {{ $session->name }} </td></tr>
                                <tr><th> Description </th><td> {{ $session->description }} </td></tr>
                                <tr><th>Start Date</th><td> {{ $session->start_date }} </td></tr>
                                <tr><th>End Date</th><td> {{ $session->end_date }} </td></tr>
                                <tr><th>Start Time</th><td> {{ $session->start_time }} </td></tr>
                                <tr><th>End Time</th><td> {{ $session->end_time }} </td></tr>
                                <tr><th>Guest Allowed</th><td> {{ $session->guest_allowed }} </td></tr>
                                <tr><th>Location</th><td> {{ $session->location }} </td></tr>
                                <tr><th>Latitude</th><td> {{ $session->latitude }} </td></tr>
                                <tr><th>Image 1.</th>
                                    <?php if (!empty($session->images_1)) { ?>
                                        <td><img width="50" src="{{url('uploads/session/'.$session->images_1)}}"></td>
                                    <?php } else { ?>
                                        <td><img width="50" src="{{url('uploads/session/noimage.png')}}"></td>                           
                                    <?php } ?>
                                </tr>
                                <tr><th>Image 2.</th>
                                    <?php if (!empty($session->images_2)) { ?>
                                        <td><img width="50" src="{{url('uploads/session/'.$session->images_2)}}"></td>
                                    <?php } else { ?>
                                        <td><img width="50" src="{{url('uploads/session/noimage.png')}}"></td>                           
                                    <?php } ?>
                                </tr>
                                <tr><th>Image 3.</th>
                                    <?php if (!empty($session->images_3)) { ?>
                                        <td><img width="50" src="{{url('uploads/session/'.$session->images_3)}}"></td>
                                    <?php } else { ?>
                                        <td><img width="50" src="{{url('uploads/session/noimage.png')}}"></td>                           
                                    <?php } ?>
                                </tr>
                                <tr><th>Image 4.</th>
                                    <?php if (!empty($session->images_4)) { ?>
                                        <td><img width="50" src="{{url('uploads/session/'.$session->images_4)}}"></td>
                                    <?php } else { ?>
                                        <td><img width="50" src="{{url('uploads/session/noimage.png')}}"></td>                           
                                    <?php } ?>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
