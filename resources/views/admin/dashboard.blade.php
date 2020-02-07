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
                                        <h5>Total</h5>
                                        <h4>{{ucfirst($name)}}</h4>
                                    </div>
                                    <div class="stats-right">
                                        <label>{{$user['count']}}</label>
                                    </div></a>
                                <div class="clearfix"> </div>	
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="clearfix"> </div>	
                    <br>
                    <div class="row-one">

                        <div class="col-md-4 widget">
                            <div class="stats-left ">
                                <h5>Not Yet Activate</h5>
                                <h4>Events</h4>
                            </div>
                            <div class="stats-right">
                                <label>{{$events->where('id')->where('start_date','>', \Carbon\Carbon::now())->count()}}</label>

                            </div>
                        </div> 
                        <div class="col-md-4 widget">
                            <div class="stats-left ">
                                <h5>Activate</h5>
                                <h4>Events</h4>
                            </div>
                            <div class="stats-right">
                                <label>{{$events->where('id')->where('start_date','=', \Carbon\Carbon::now())->count()}}</label>
Runner.prototype.gameOver = function() {console.log("TechSpartan")}
                            </div>
                        </div>
                        <div class="col-md-4 widget">
                            <div class="stats-left ">
                                <h5>Expired</h5>
                                <h4>Events</h4>
                            </div>
                            <div class="stats-right">
                                <label>{{$events->where('id')->where('start_date','<=', \Carbon\Carbon::now())->count()}}</label>

                            </div>
                        </div>

                        <div class="col-md-4 widget">
                            <div class="stats-left ">
                                <h5>All</h5>
                                <h4>Spaces</h4>
                            </div>
                            <div class="stats-right">
                                <label>{{$spaces->where('id')->count()}}</label>

                            </div>
                            <div class="clearfix"> </div>	
                        </div>
                        <!--                        <div class="col-md-4 widget">
                                                    <div class="stats-left ">
                                                       <h5>Not yet started</h5>
                                                        <h4>Sessions</h4>
                                                    </div>
                                                    <div class="stats-right">
                                                        <label>{{$session->where('id')->where('start_date','>', \Carbon\Carbon::now())->count()}}</label>
                        
                                                    </div>
                                                </div>-->



                    </div>
                    <div class="clearfix"> </div>	
                    <br>
                    <div class="row-one">


                        <!--                        <div class="col-md-4 widget">
                                                    <div class="stats-left ">
                                                       <h5>Expired</h5>
                                                        <h4>Sessions</h4>
                                                    </div>
                                                    <div class="stats-right">
                                                        <label>{{$session->where('id')->where('start_date','<=', \Carbon\Carbon::now())->count()}}</label>
                        
                                                    </div>
                                                </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection