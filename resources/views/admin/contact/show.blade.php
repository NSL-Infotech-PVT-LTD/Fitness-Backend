@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Contact {{ $contact->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/contact') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                  
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $contact->id }}</td>
                                </tr>
                                <tr><th> Message </th><td> {{ $contact->message }} </td></tr>
                                <tr><th> Media </th>
                                    <?php if(!empty($contact->media)) { ?>
                                    <td><img width="150" src="{{url('uploads/contact/'.$contact->media)}}"></td>
                                    <?php } else{ ?>
                            <td><img width="50" src="{{url('noimage.png')}}"></td>  
                                    <?php } ?>
                                </tr>
                                <tr><th> Sender's Name </th><td> {{ $createdBy }} </td></tr>
                                <tr><th> Sender's Email </th><td> {{ $createdEmail }} </td></tr>
                                <tr><th> Sender's Phone</th><td> {{ $createdPhone }} </td></tr>
                                <tr><th> Send At</th><td> {{ $contact->created_at }} </td></tr>
                                <tr><th> Sender Type</th><td> {{ $createdType }} </td></tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
