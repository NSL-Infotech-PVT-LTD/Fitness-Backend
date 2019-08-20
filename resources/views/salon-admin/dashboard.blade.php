@extends('layouts.salon-backend')
@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <div class="row-one">

                        <div class="col-md-4 widget states-last">
                            <div class="stats-left">
                                <h5>Today</h5>
                                <h4>Orders</h4>
                            </div>
                            <div class="stats-right">
                                <label><?= $orders ?></label>
                            </div>
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
