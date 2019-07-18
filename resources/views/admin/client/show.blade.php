@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Category {{ $clients->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/clientdashboard') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $clients->id }}</td>
                                    </tr>
                                    <tr><th> Name </th><td> {{ $clients->firstname }} </td></tr>
                                    <tr><th>LastName</th><td> {{ $clients->lastname}} </td></tr>
                                    <tr><th>Phone</th><td> {{ $clients->phone}} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
