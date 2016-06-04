@extends('header')
@section('title', '未缴费罚款')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/punish/uncharged.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">未缴费罚款</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search"  method="get" action="{{ url('punish/uncharged-search') }}">
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="text" class="form-control"  value="{{ $_GET['company_name'] or '' }}" name="company_name" placeholder="公司名">&nbsp;
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
                <th>公司名</th>
                <th>罚款原因</th>
                <th>金额</th>
                <th>罚款人</th>
                <th>时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($unchargedLists as $unchargedList)
                <tr>
                    <td>{{ $unchargedList->company->company_name }}</td>
                    <td>{{ $unchargedList->reason }}</td>
                    <td>{{ $unchargedList->money }}</td>
                    <td>{{ $unchargedList->user_id }}</td>
                    <td>{{ substr($unchargedList->created_at, 0, 10) }}</td>
                    <td>{{ $unchargedList->punish_remark }}</td>
                    <td>
                        <button class="btn btn-success btn-xs charge-button" charge_id="{{ $unchargedList->punish_id }}">缴费</button>
                        <a href="{{ url('punish/edit-remark/'.$unchargedList->punish_id)}}" class="btn btn-success btn-xs">修改备注</a>
                        <a href="{{ url('punish/cancel/'.$unchargedList->punish_id)}}" class="btn btn-danger btn-xs">撤销</a>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $unchargedLists->appends([
                        'company_name'=>isset($_GET['company_name']) ? $_GET['company_name'] : '',
                    ])->render() !!}
    </div>
@endsection
@section('modal')
        <!-- charge modal -->
    <div id="charge-modal" class="modal bs-example-modal-sm fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">缴费确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要缴费吗？
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="charge-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <p>共有 {{ $count['totalNumber'] }} 条记录</p>
    <p>共计 {{ $count['totalMoney'] }} 元</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //缴费
        ajaxCharge('{{ url('punish/charge/') }}');
    </script>
@endsection