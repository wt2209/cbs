@extends('header')
@section('title', '已缴费罚款')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/common.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">已缴费罚款</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search"  method="get" action="{{ url('punish/charged-search') }}">
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
                <th>开单时间</th>
                <th>缴费时间</th>
                <th>备注</th>
            </tr>
            </thead>
            @foreach ($chargedLists as $chargedList)
                <tr>
                    <td>{{ $chargedList->company->company_name }}</td>
                    <td>{{ $chargedList->reason }}</td>
                    <td>{{ $chargedList->money }}</td>
                    <td>{{ $chargedList->user_id }}</td>
                    <td>{{ substr($chargedList->created_at, 0, 10) }}</td>
                    <td>{{ substr($chargedList->charged_at, 0, 10) }}</td>
                    <td>{{ $chargedList->punish_remark }}</td>
                </tr>
            @endforeach
        </table>
        {!! $chargedLists->appends([
                        'company_name'=>isset($_GET['company_name']) ? $_GET['company_name'] : '',
                    ])->render() !!}
    </div>
@endsection
@section('bottom')
    <p>共有 {{ $count['totalNumber'] }} 条记录</p>
    <p>共计 {{ $count['totalMoney'] }} 元</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection