@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">User</div>
                <div class="card-body">

                    <a href="{{ url(url()->previous()) }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" title="Edit User"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                    {!! Form::open([
                    'method' => 'DELETE',
                    'url' => ['/admin/users', $user->id],
                    'style' => 'display:inline'
                    ]) !!}
                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                    'type' => 'submit',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => 'Delete User',
                    'onclick'=>'return confirm("Confirm delete?")'
                    ))!!}
                    {!! Form::close() !!}
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            </thead>
                            <tbody>
                                <?php foreach (['id', 'name', 'email', 'phone', 'location', 'latitude', 'longitude', 'business_hour_starts', 'business_hour_ends', 'bio', 'expertise_years', 'hourly_rate', 'profession', 'experience_detail', 'training_service_detail'] as $column): ?>
                                    <tr>
                                        <th>{{ucfirst(str_replace('_',' ',$column))}}.</th>
                                        <td>{{ $user->$column }}</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th>Profile Image.</th>
                                    <td><img width="50" src="{{url('uploads/coach/profile_image/'.$user->profile_image)}}"></td>
                                </tr>
                                <tr>
                                    <th>Police Doc.</th>

                                    <td><a href="{{url('uploads/coach/police_doc/'.$user->police_doc)}}" target="_blank"><img width="50" src="{{url('click.jpeg')}}"></a></td>
                                </tr>


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
