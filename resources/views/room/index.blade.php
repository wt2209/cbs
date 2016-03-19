@extends('header')
@section('title', '房间明细')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">房间明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        房间号：
                        <input type="text" class="form-control">&nbsp;&nbsp;&nbsp;
                        <label class="no-bold"><input type="radio" name="status" />空房间</label>&nbsp;&nbsp;&nbsp;
                        <label class="no-bold"><input type="radio" name="status" />正在使用的房间</label>&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:location='{{ url('room/add') }}';">新增房间</button>
        <button class="btn btn-danger btn-sm">从文件导入</button>
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
                <tr class="active">
                    <th>房间号</th>
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
                        <td>{{ $room->building }}-{{ $room->room_number }}</td>
                        <td>空房间</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $room->room_remark }}</td>
                        <td>
                            <a href="{{ url('room/edit/'.$room->room_id) }}" class="btn btn-success btn-xs">修改备注</a>
                            <a href="javascript:;" room_id="{{ $room->room_id }}" class="btn btn-danger btn-xs">删除</a>
                        </td>
                    </tr>
                @else
                    {{--正在使用--}}
                {{--TODO 需要从数据库中调--}}
                    <tr>
                        <td>{{ $room->building }}-{{ $room->room_number }}</td>
                        <td><strong style="color:#0099cc">正在使用</strong></td>
                        <td>{{ $room->company->company_name }}</td>
                        <td>{{ $room->company->linkman }}</td>
                        <td>{{ $room->company->linkman_tel }}</td>
                        <td>{{ $room->room_remark }}</td>
                        <td>
                            <a href="{{ url('room/edit/'.$room->room_id) }}" class="btn btn-success btn-xs">修改备注</a>
                            <a href="javascript:;" room_id="{{ $room->room_id }}" class="btn btn-danger btn-xs">删除</a>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>

    <!-- delete modal -->
    <div id="modal" class="modal bs-example-modal-sm">
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
                    <button id="modal-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <p>haha </p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //删除模态框
{{--        //{{ url('room/del/'.$room->room_id) }}--}}
        var roomId = 0;
        $('.btn-danger').click(function(){
            $('#modal').modal('show');
            roomId = $(this).attr('room_id');
        })
        $('#modal-confirm').click(function(){
            $('#modal').modal('hide');
            maskShow();
            $.get('{{ url('room/remove/') }}', 'room_id=' + roomId, function(e){
                maskHide();
                popdown({'message':e.message, 'status': e.status, 'callback':function(){
                    if (e.status) {
                        location.reload(true);
                    }
                }});
            }, 'json');
        })

    </script>
@endsection