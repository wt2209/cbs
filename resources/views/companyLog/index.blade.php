@extends('header')
@section('title', '承包商公寓管理系统--公司房间变动记录')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/companyLog/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">公司房间变动记录</a></li>
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
                        <input type="text" class="form-control"  placeholder="公司名称">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-danger btn-sm">从文件导入</button>
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>承包商公司</th>
                <th>操作人</th>
                <th>操作类型</th>
                <th>操作时间</th>
                <th>老房间</th>
                <th>新房间</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($companyLogs as $companyLog)
                <tr>
                    <td>{{$companyLog->company_name}}</td>
                    <td>{{$companyLog->user_id}}</td>
                    <td>
                        @if($companyLog->type == 1)
                            入住
                        @elseif($companyLog->type == 2)
                            更改
                        @elseif($companyLog->type == 3)
                            退房
                        @elseif($companyLog->type == 4)
                            删除
                        @endif
                    </td>
                    <td>{{substr($companyLog->created_at, 0, 10)}}</td>
                    <td>{{$companyLog->old_rooms}}</td>
                    <td>{{$companyLog->new_rooms}}</td>
                    <td>
                        <a href="" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>
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
    <p>共有 {{ count($companyLogs) }} 条记录</p>
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