@extends('header')
@section('title', '角色明细')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">角色明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
   {{-- <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">

            </div>
        </div>
    </nav>--}}
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>角色名</th>
                <th>说明</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->role_name }}</td>
                    <td>
                        {{ $role->role_description }}
                    </td>
                    <td>
                        {{ substr($role->created_at, 0, 10) }}
                    </td>
                    <td>
                        @if ($role->id == 1)
                            <a href="{{ url('user/edit-role-permission/'.$role->id) }}" class="btn btn-success btn-xs" disabled="disabled">修改权限</a>
                            <a href="javascript:;" delete_id="{{ $role->id }}" class="btn btn-danger btn-xs delete-button" disabled="disabled">删除</a>
                        @else
                            <a href="{{ url('user/edit-role-permission/'.$role->id) }}" class="btn btn-success btn-xs">修改权限</a>
                            <a href="javascript:;" delete_id="{{ $role->id }}" class="btn btn-danger btn-xs delete-button">删除</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    @endsection
    @section('modal')
            <!-- delete modal -->
    <div id="delete-modal" class="modal fade bs-example-modal-sm">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">删除确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要删除吗？
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="delete-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //删除模态框
        ajaxDelete('{{ url('user/remove-role/') }}');

    </script>
@endsection