@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Contact us portal</div>
              

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
                                <th></th>
                            </tr>
                        </thead>
                    </table>
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
            ajax: "{{ route('contact.index') }}",
            columns: [
            {data: 'id', name: 'id'},
<?php foreach ($rules as $rule): ?>
    <?php if ($rule == 'message'): ?>
                    {data: 'message', name: 'message', orderable: false, searchable: false},
    <?php else: ?>
                    {data: "{{$rule}}", name: "{{$rule}}"},
    <?php endif; ?>
<?php endforeach; ?>
            {data: 'action', name: 'action', orderable: false, searchable: false},
          
            
            ,
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
            swal.fire("Deleted!", "Query has been deleted", "success");
            table.ajax.reload(null, false);
            }
            }
    });
    }
    });
    });
   
    }
    );
</script>
@endsection
