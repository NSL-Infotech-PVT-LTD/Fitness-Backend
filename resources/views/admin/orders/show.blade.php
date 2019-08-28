@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Order {{ $order->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/orders') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>

                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Order ID</th><td>{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <th> Customer</th><td> {{ $order->customer_id }} </td>
                                </tr>
                                <tr>
                                    <th> Payment </th><td> {{ $order->payment }} </td>
                                </tr>
                                <tr>
                                    <th> Discounts </th><td> {{ $order->discounts }} </td>
                                </tr>
                                <tr>
                                    <th> Tax </th><td> {{ $order->tax }} </td>
                                </tr>
                                <tr>
                                    <th> total </th><td> {{ $order->total_paid }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails as $orderDetail): ?>
                                    <tr>
                                        <td> {{ $orderDetail->product_id->id }} </td>
                                        <td><a href="{{route('products.show',$orderDetail->product_id->id)}}"> {{ $orderDetail->product_id->name }} </a></td>
                                        <td> {{ $orderDetail->quantity }} </td>
                                        <td> {{ $orderDetail->product_id->price*$orderDetail->quantity }} </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
