@extends('header')
@section('title', '水电费明细')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/utility/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">水电费明细</a></li>
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
                        <input type="text" class="form-control" placeholder="房间号">&nbsp;&nbsp;&nbsp;
                        <input type="text" class="form-control" placeholder="公司名称">&nbsp;&nbsp;&nbsp;
                        <input type="text" class="form-control" placeholder="月份，格式为：2016-3">&nbsp;&nbsp;&nbsp;
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
                <th>房间号</th>
                <th>所属公司</th>
                <th>公司状态</th>
                <th>费用月份</th>
                <th>电费</th>
                <th>水费</th>
                <th>合计</th>
                <th>是否缴费</th>
                <th>缴费时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach($utilities as $utility)
                <tr>
                    <td>{{ $utility->building }}-{{ $utility->room_number }}</td>
                    <td>{{ $utility->company_name }}</td>
                    <td>
                        @if($utility->is_quit === 1)
                            <span style="color:red">已退租</span>
                        @elseif($utility->is_quit === 0)
                            正常
                        @endif
                    </td>
                    <td>{{ $utility->year }}-{{ $utility->month }}</td>
                    <td>{{ $utility->electric_money }}</td>
                    <td>{{ $utility->water_money }}</td>
                    <td>{{ $utility->water_money + $utility->electric_money }}</td>
                    <td>
                        @if($utility->is_charged === 1)
                            √
                        @elseif($utility->is_charged === 0)
                            <span style="color:red">×</span>
                        @endif
                    </td>
                    <td>
                        @if($utility->is_charged)
                            {{ $utility->charge_time }}
                        @endif
                    </td>
                    <td>{{ $utility->utility_remark }}</td>
                    <td>
                        @if ($utility->is_charged)
                            <button class="btn btn-success btn-xs charge-button" disabled="disabled" utility_id="{{ $utility->utility_id }}">缴费</button>
                        @else
                            <button class="btn btn-success btn-xs charge-button" utility_id="{{ $utility->utility_id }}">缴费</button>
                        @endif
                        <a href="{{ url('utility/edit/'.$utility->utility_id) }}" class="btn btn-primary btn-xs">修改</a>
                        <button delete_id="{{ $utility->utility_id }}" class="btn btn-danger btn-xs delete-button">删除</button>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $utilities->render() !!}
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
                    <form id="form">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="year">
                    </form>
                    <button id="charge-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
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
    <p>共有 {{ $count['total_number'] }} 条记录</p>
    <p>
        已缴费用共计 {{ $count['is_charged']['water_money'] + $count['is_charged']['electric_money'] }} 元，
        其中电费 {{ $count['is_charged']['electric_money'] }} 元，
        水费 {{ $count['is_charged']['water_money'] }} 元。
    </p>
    <p>
        尚未缴费用共计 {{ $count['no_charged']['water_money'] + $count['no_charged']['electric_money'] }} 元，
        其中电费 {{ $count['no_charged']['electric_money'] }} 元，
        水费 {{ $count['no_charged']['water_money'] }} 元。
    </p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //缴费
        ajaxCharge('{{ url('utility/charge-single-room/') }}');
        //删除
        ajaxDelete('{{ url('utility/delete/') }}');

    </script>
@endsection