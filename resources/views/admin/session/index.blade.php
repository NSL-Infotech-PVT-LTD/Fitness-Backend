@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Session</div>
                <div class="card-body">                  
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
                                    <th>status</th>
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
            ajax: "{{ route('session.index') }}",
            columns: [
            {data: 'id', name: 'id'},
<?php foreach ($rules as $rule): ?>
                {data: "{{$rule}}", name: "{{$rule}}"},
<?php endforeach; ?>
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false
            },
            ]
    });
//deleting data
    $('.data-table').on('click', '.btnDelete[data-remove]', function (e) {
    e.preventDefault();
    var url = $(this).data('remove');
    swal.fire({
    title: "Are you sure want to remove this item?",
            text: "Data will be Temporary Deleted!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Confirm",
            cancelButtonText: "Cancel",
    }).then((result) => {
    Swal.showLoading();
    if (result.value) {
    $.ajax({
    url: url,
            type: 'DELETE',
            dataType: 'json',
            data: {method: '_DELETE', submit: true, _token: '{{csrf_token()}}'},
            success: function (data) {
            if (data == 'Success') {
            swal.fire("Deleted!", "Session has been deleted", "success");
            table.ajax.reload(null, false);
            }
            }
    });
    }
    });
    });
    $('.data-table').on('click', '.changeStatus', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var status = $(this).attr('data-status');
    Swal.fire({
    title: 'Are you sure you wanted to change status?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ' + status + ' it!'
    }).then((result) => {
    Swal.showLoading();
    if (result.value) {
    var form_data = new FormData();
    form_data.append("id", id);
    form_data.append("status", status);
    form_data.append("_token", $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
    url: "{{route('session.changeStatus')}}",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
//                        Swal.showLoading();
            },
            success: function (data)
            {
            Swal.fire(
                    status + ' !',
                    'Session has been ' + status + ' .',
                    'success'
                    ).then(() => {
            table.ajax.reload(null, false);
            });
            }
    });
    }
    });
    });
    }
    );
</script>
@endsection
