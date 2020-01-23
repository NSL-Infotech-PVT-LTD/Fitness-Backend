@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Space {{ $space->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/spaces') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/admin/spaces/' . $space->id . '/edit') }}" title="Edit Space"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                    {!! Form::open([
                    'method'=>'DELETE',
                    'url' => ['admin/spaces', $space->id],
                    'style' => 'display:inline'
                    ]) !!}
                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                    'type' => 'submit',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => 'Delete Space',
                    'onclick'=>'return confirm("Confirm delete?")'
                    ))!!}
                    {!! Form::close() !!}
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tr><th>ID</th><td>{{ $space->id }}</td></tr>
                            <tr><th>Name </th><td> {{ $space->name }} </td></tr>
                            <tr><th>Description </th><td> {{ $space->description }} </td></tr>
                            <tr><th>Price Hourly</th><td> {{ $space->price_hourly }} </td></tr>
                            <tr><th>Price Daily</th><td> {{ $space->price_daily }} </td></tr>
                            <tr><th>Location</th><td> {{ $space->location }} </td></tr>
                            <tr><th>Latitude</th><td> {{ $space->latitude }} </td></tr>
                            <tr><th>Open Hours From</th><td> {{ $space->open_hours_from }} </td></tr>
                            <tr><th>Open Hours To</th><td> {{ $space->open_hours_to }} </td></tr>
                            <tr><th>Image 1.</th>
                                <?php if (!empty($space->images_1)) { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/'.$space->images_1)}}"></td>
                                <?php } else { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/noimage.png')}}"></td>                           
                                <?php } ?>
                            </tr>
                            <tr><th>Image 2.</th>
                                <?php if (!empty($space->images_2)) { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/'.$space->images_2)}}"></td>
                                <?php } else { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/noimage.png')}}"></td>                           
                                <?php } ?>
                            </tr>
                            <tr><th>Image 3.</th>
                                <?php if (!empty($space->images_3)) { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/'.$space->images_3)}}"></td>
                                <?php } else { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/noimage.png')}}"></td>                           
                                <?php } ?>
                            </tr>
                            <tr><th>Image 4.</th>
                                <?php if (!empty($space->images_4)) { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/'.$space->images_4)}}"></td>
                                <?php } else { ?>
                                    <td><img width="50" src="{{url('uploads/spaces/noimage.png')}}"></td>                           
                                <?php } ?>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
