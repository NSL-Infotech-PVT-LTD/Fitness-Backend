@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Event {{ $event->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/events') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/admin/events/' . $event->id . '/edit') }}" title="Edit Event"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                    {!! Form::open([
                    'method'=>'DELETE',
                    'url' => ['admin/events', $event->id],
                    'style' => 'display:inline'
                    ]) !!}
                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                    'type' => 'submit',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => 'Delete Event',
                    'onclick'=>'return confirm("Confirm delete?")'
                    ))!!}
                    {!! Form::close() !!}
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $event->id }}</td>
                                </tr>
                                <tr><th> Name </th><td> {{ $event->name }} </td></tr>
                                <tr><th> Description </th><td> {{ $event->description }} </td></tr>
                                <tr><th> Start Date </th><td> {{ $event->start_date }} </td></tr>
                                <tr><th> End Date </th><td> {{ $event->end_date }} </td></tr>
                                <tr><th> Name </th><td> {{ $event->name }} </td></tr>
                                <tr><th> Description </th><td> {{ $event->description }} </td></tr>
                                <tr><th> Start Date </th><td> {{ $event->start_date }} </td></tr>
                                <tr><th> End Date </th><td> {{ $event->end_date }} </td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
