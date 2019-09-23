@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Services</div>
                <div class="card-body">
                    <a href="{{ url('/admin/services/create') }}" class="btn btn-success btn-sm" title="Add New Service">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </a>

                    <br/>
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-borderless data-table">
                            <thead>
                                <tr> 
                                    <th>ID</th>
                                    <?php foreach ($rules as $rule): ?>
                                        <th>{{ucfirst($rule)}}</th>
                                    <?php endforeach; ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('services.index') }}",
            columns: [
                {data: 'id', name: 'id'},
<?php foreach ($rules as $rule): ?>
                    {data: "{{$rule}}", name: "{{$rule}}"},
<?php endforeach; ?>
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

//deleting data
        $('.data-table').on('click', '.btnDelete[data-remove]', function (e) {
            e.preventDefault();
            var url = $(this).data('remove');
            swal({
                title: "Are you sure want to remove this item?",
                text: "Data will be Temporary Deleted!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false,
            },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                dataType: 'json',
                                data: {method: '_DELETE', submit: true, _token: '{{csrf_token()}}'},
                                success: function (data) {
                                    if (data == 'Success') {
                                        swal("Deleted!", "Service has been deleted", "success");
                                        table.ajax.reload(null, false);
                                    }
                                }
                            });
                        } else {

                            swal("Cancelled", "You Cancelled", "error");
                        }

                    });
        });


    });
</script>

@endsection
