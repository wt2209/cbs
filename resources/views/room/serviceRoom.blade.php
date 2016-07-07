@extends('header')
@section('title', '服务用房明细')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">服务用房明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search"  method="get" action="{{ url('room/search') }}">
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="room_type" value="3">
                        <input type="text" class="form-control"  value="{{ $_GET['room_name'] or '' }}" name="room_name" placeholder="房间号">&nbsp;或者
                        <select name="room_status" class="form-control">
                            <option value="0">全部房间</option>
                            <option value="1" @if(isset($_GET['room_status'])&&$_GET['room_status'] == 1) selected=""@endif>正在使用</option>
                            <option value="2" @if(isset($_GET['room_status'])&&$_GET['room_status'] == 2) selected=""@endif>空房间</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:location='{{ url('room/add') }}';">新增房间</button>
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>房间名</th>
                <th>使用状态</th>
                <th>所属公司</th> {{--允许有多个公司--}}
                <th>公司联系人</th>{{--若有多个公司，则有多个联系人--}}
                <th>联系人电话</th>{{--同上--}}
                <th>房间备注</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($rooms as $room)
                @if ($room->company_id == 0)
                    {{--空房--}}
                    <tr>
                        <td>{{ $room->room_name }}</td>
                        <td>空房间</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $room->room_remark }}</td>
                        <td>
                            <a href="{{ url('room/edit/'.$room->room_id) }}" class="btn btn-success btn-xs">修改备注</a>
                            {{--<a href="javascript:;" delete_id="{{ $room->room_id }}" class="btn btn-danger btn-xs delete-button">删除</a>--}}
                        </td>
                    </tr>
                @else
                    {{--正在使用--}}
                    <tr>
                        <td>{{ $room->room_name }}</td>
                        <td><strong style="color:#0099cc">正在使用</strong></td>
                        <td>{{ $room->company->company_name }}</td>
                        <td>{{ $room->company->linkman }}</td>
                        <td>{{ $room->company->linkman_tel }}</td>
                        <td>{{ $room->room_remark }}</td>
                        <td>
                            <a href="{{ url('room/edit/'.$room->room_id) }}" class="btn btn-success btn-xs">修改备注</a>
                            {{--<a href="javascript:;" delete_id="{{ $room->room_id }}" class="btn btn-danger btn-xs delete-button">删除</a>--}}
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
        {!! $rooms->appends([
                'room_type'=>isset($_GET['room_type']) ? $_GET['room_type'] : 1,
                'room_name'=>isset($_GET['room_name']) ? $_GET['room_name'] : '',
                'room_status'=>isset($_GET['room_status']) ? $_GET['room_status'] :0
            ])->render() !!}
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
@section('bottom')
    <p>共有 {{ $count['all'] }} 个房间</p>
    <p>剩余 {{ $count['empty'] }} 个空房间</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //删除模态框
        ajaxDelete('{{ url('room/remove/') }}');

    </script>
@endsection