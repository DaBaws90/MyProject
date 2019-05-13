@extends('layouts.master')

@section('content')
    <table class="table table-bordered" id="products-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
    </table>
@stop

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){
        $('#products-table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "pageLength": 10,
            //"iDisplayLength": 10,
            "deferRender": true,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "ajax": {
                url: "/fetchProducts",
                type: "POST",
                dataSrc : 'data',
                data:{ _token: "{!! @csrf !!}"}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' }
            ],
        });
    });
</script>    
@endpush