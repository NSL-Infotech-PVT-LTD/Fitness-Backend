@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Category {{ $freelancer->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/display') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
<!--                        <a href="{{ url('/admin/categories/' . $freelancer->id . '/edit') }}" title="Edit Category"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>-->
                     
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $freelancer->id }}</td>
                                    </tr>
                                    <tr><th> Name </th><td> {{ $freelancer->firstname }} </td></tr>
                                    <tr><th>LastName</th><td> {{ $freelancer->lastname}} </td></tr>
                                    <tr><th>Phone</th><td> {{ $freelancer->phone}} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
