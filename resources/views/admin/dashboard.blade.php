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
                        <div class="col-md-4 widget">
                            <a href="{{url('admin/users/role/2')}}"> <div class="stats-left ">
                                    <h5>Today</h5>
                                    <h4>Salon Admin</h4>
                                </div>
                                <div class="stats-right">
                                    <label>{{$salonadmin}}</label>
                                </div></a>
                            <div class="clearfix"> </div>	
                        </div>
                        <div class="col-md-4 widget states-mdl">
                            <a href="{{url('admin/users/role/3')}}">
                                <div class="stats-left">
                                    <h5>Today</h5>
                                    <h4>Customer</h4>
                                </div>
                                <div class="stats-right">
                                    <label>{{$customers}}</label>
                                </div>
                            </a>
                            <div class="clearfix"> </div>	
                        </div>
                        <div class="col-md-4 widget states-last">
                            <a href="{{url('admin/orders')}}">
                                <div class="stats-left">
                                    <h5>Today</h5>
                                    <h4>Orders</h4>
                                </div>
                                <div class="stats-right">
                                    <label><?= $orders ?></label>
                                </div>
                            </a>
                            <div class="clearfix"> </div>	
                        </div>
                        
                        <div class="col-md-4 widget states-last">
                            <a href="{{url('admin/appointments')}}">
                                <div class="stats-left">
                                    <h5>Today</h5>
                                    <h4>Appointments</h4>
                                </div>
                                <div class="stats-right">
                                    <label><?= $appointmentsAccepted ?></label>
                                </div>
                            </a>
                            <div class="clearfix"> </div>	
                        </div>
                         <div class="col-md-4 widget states-last">
                            <a href="{{url('admin/appointments')}}">
                                <div class="stats-left">
                                    <h5>Cancelled</h5>
                                    <h4>Appointments</h4>
                                </div>
                                <div class="stats-right">
                                    <label><?= $appointmentsRejected ?></label>
                                </div>
                            </a>
                            <div class="clearfix"> </div>	
                        </div>
                        <div class="clearfix"> </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection