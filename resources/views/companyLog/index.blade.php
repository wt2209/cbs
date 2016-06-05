@extends('header')
@section('title', '承包商公寓管理系统--公司房间变动记录')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/companyLog/index.css') }}"/>
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
    <div class="function-area">
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th width="80">承包商公司</th>
                <th width="50">操作人</th>
                <th width="60">操作类型</th>
                <th width="90">操作时间</th>
                <th>老房间</th>
                <th>新房间</th>
                <th width="50">操作</th>
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