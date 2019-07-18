@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Category</div>
                <div class="card-body">
     
                    <a href="{{ url('/admin/categories')}}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/admin/subcategory/'.app('request')->id)}}" title="add new"><button class="btn btn-warning btn-sm"><i class="fa fa-plus" aria-hidden="true"></i>Add New</button></a>
                    
                    <br/>
                    <br/>
  
                
                   <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @foreach($subcategory as $categories)
                                <tr>
                                    <td>{{ $categories->id }}</td>
                                    <td>{{ $categories->name }}</td>
                                    <td>
                                        <a href="{{ url('/admin/view/' . $categories->id) }}" title="View Category"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a> 
                                    
                                        <a href="{{ url('admin/subcategory/' . $categories->id . '/edit') }}" title="Edit Category"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                   
                                        {!! Form::open([
                                        'method' => 'DELETE',
                                        'url' => ['admin/subcategorydelete', $categories->id],
                                        'style' => 'display:inline'
                                        ]) !!}
                                        {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger btn-sm',
                                        'title' => 'Delete Category',
                                        'onclick'=>'return confirm("Confirm delete?")'
                                        )) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                               @endforeach
                            </tbody>
                           
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
