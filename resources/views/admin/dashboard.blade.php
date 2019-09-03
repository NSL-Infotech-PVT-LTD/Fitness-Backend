@extends('layouts.backend')
@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    <div class="row-one">
                        <?php foreach ($users as $name => $user): ?>
                            <div class="col-md-4 widget">
                                <a href="{{url('admin/users/role/'.$user['role_id'])}}"> <div class="stats-left ">
                                        <h5>Today</h5>
                                        <h4>{{$name}}</h4>
                                    </div>
                                    <div class="stats-right">
                                        <label>{{$user['count']}}</label>
                                    </div></a>
                                <div class="clearfix"> </div>	
                            </div>
                        <?php endforeach; ?>
                        <div class="clearfix"> </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection