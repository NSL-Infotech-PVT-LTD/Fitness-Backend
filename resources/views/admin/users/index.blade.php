@extends('layouts.backend')

@section('content')
<style>
    button.btn.btn-secondary.fafa {
        display: inline-block;
        float: right;
        position: absolute;
    }
</style>

<div class="container-fluid">
    <div class="row">
        @include('admin.sidebar')
        <div class="col-md-12">
            <div class="card">
                <?php
                $user = '';
                if (\Request::fullUrl() == 'http://localhost/patchwork/public/admin/users/role/1') {
                    $user = 'Admin User';
                }
                if (\Request::fullUrl() == 'http://localhost/patchwork/public/admin/users/role/2') {
                    $user = 'Freelancer User';
                }
                if (\Request::fullUrl() == 'http://localhost/patchwork/public/admin/users/role/3') {
                    $user = 'Client User';
                }
                ?>
                <div class="card-header  text-center"><h3 >{{$user}}</h3></div>
                <div class="card-body">
                    <?php if (isset($role_id)): ?>
                        <?php if ($role_id === "1"): ?>
                            <a href="{{ url('/admin/users/create') }}" class="btn btn-success btn-sm" title="Add New User">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    {!! Form::open(['method' => 'GET', 'url' => '/admin/users', 'class' => 'form-inline my-2 m-r  pull-right', 'role' => 'search'])  !!}
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                        <span class="input-group-append">
                            <button class="btn btn-secondary fafa" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}
                </div>

                <br/>
                <br/>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>LastName</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->firstname }}</td>
                                <td>{{ $item->lastname }}</td>
                                <td>{{ $item->email }}</td>
                                <td>
                                    <a href="{{ url('/admin/users/' . $item->id) }}" title="View User"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                    <a href="{{ url('/admin/users/' . $item->id . '/edit') }}" title="Edit User"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    {!! Form::open([
                                    'method' => 'DELETE',
                                    'url' => ['/admin/users', $item->id],
                                    'style' => 'display:inline'
                                    ]) !!}
                                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete User',
                                    'onclick'=>'return confirm("Confirm delete?")'
                                    )) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $users->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
