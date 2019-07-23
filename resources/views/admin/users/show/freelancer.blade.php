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
                                <?php foreach (['id', 'full_name', 'email', 'experience', 'hourly_rate', 'bio'] as $column): ?>
                                    <tr>
                                        <th>{{ucfirst(str_replace('_',' ',$column))}}.</th>
                                        <td>{{ $user->$column }}</td> 
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th>Profile.</th>
                                    <td><img width="50" src="{{url('uploads/freelancer/profile_pic/'.$user->profile_pic)}}"></td> 
                                </tr>
                                <tr>
                                    <th>Portfolio.</th>
                                    <td>
                                        <ul>
                                            <?php
                                            if (!is_null($user->portfolio_image)):
                                                foreach ($user->portfolio_image as $portfolio):
                                                    ?><li><img width="50" src="{{url('uploads/freelancer/portfolio/'.$portfolio)}}"></li>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </ul>
                                    </td> 
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
