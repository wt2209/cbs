@extends('header')
@section('title', '承包商公寓管理系统--公司房间变动记录')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/companyLog/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">公司房间变动记录</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company-log/index') }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('company-log/search') }}">
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="text" class="form-control" value="{{ $_GET['company_name'] or '' }}" name="company_name"  placeholder="公司名称">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
{{--    <div class="function-area">
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>--}}
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>房间号</th>
                <th>房间类型</th>
                <th>承包商公司</th>
                <th>变动人</th>
                <th>变动类型</th>
                <th>变动时电表底数</th>
                <th>变动时水表底数</th>
                <th>变动时间</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($companyLogs as $companyLog)
                <tr>
                    <td>{{ $companyLog->room->room_name}}</td>
                    <td>
                        @if($companyLog->room->room_type == 1)
                            居住
                        @elseif($companyLog->room->room_type == 2)
                            餐厅
                        @elseif($companyLog->room->room_type == 3)
                            服务
                        @endif
                    </td>
                    <td>{{ $companyLog->company->company_name}}</td>
                    <td>{{ $companyLog->user->user_name}}</td>
                    <td>
                        @if($companyLog->room->room_type == 1)
                            @if($companyLog->room_change_type == 1)
                                增加房间
                            @elseif($companyLog->room_change_type == 2)
                                减少房间
                            @elseif($companyLog->room_change_type == 3)
                                人数变动
                            @elseif($companyLog->room_change_type == 4)
                                性别变动
                            @elseif($companyLog->room_change_type == 5)
                                性别和人数变动
                            @endif
                        @else
                            @if($companyLog->room_change_type == 1)
                                增加房间
                            @elseif($companyLog->room_change_type == 2)
                                减少房间
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($companyLog->room_change_type != 4)
                            {{ $companyLog->electric_base == 0 ? '待填':  $companyLog->electric_base }}
                        @endif
                    </td>
                    <td>
                        @if ($companyLog->room_change_type != 4)
                            {{ $companyLog->water_base == 0 ? '待填':  $companyLog->water_base }}
                        @endif
                    </td>
                    <td>{{substr($companyLog->created_at, 0, 10)}}</td>
                    <td>
                        <button delete_id="{{ $companyLog->cl_id }}" class="btn btn-danger btn-xs delete-button">删除</button>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $companyLogs->appends(['company_name' => isset($_GET['company_name']) ? $_GET['company_name'] : ''])->render() !!}
    </div>
@endsection
@section('modal')
        <!-- delete modal -->
    <div id="delete-modal" class="modal bs-example-modal-sm fade">
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
    <p>共有 {{ count($companyLogs) }} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        ajaxDelete('{{ url('company-log/delete/') }}');
    </script>
@endsection