@extends('header')

@section('title', '公司缴费')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/charge.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">公司缴费</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        @if(count($utilities) != 0)
            <p style="margin:6px 15px 0 15px;"><span style="color: red">{{ $company_name }}</span>尚有<span style="color: red">{{ $date }}</span>月份的水电费未缴纳</p>
            <p style="margin:0 15px 0 15px;">未缴纳的水电费明细如下：</p>
        @else
            <p style="margin:15px 15px 0 15px;"><span style="color: red">{{ $company_name }}</span>暂时没有水电费欠费</p>
        @endif
    </nav>

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
                <th>备注</th>
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
                    <td>{{ $utility->utility_remark }}</td>
                </tr>
            @endforeach
        </table>
        {{--{!! $utilities->render() !!}--}}
    </div>
@endsection
@section('bottom')
    @if(count($utilities) != 0)
        <p>其中，电费总计{{ $count['electric_money'] }}元,水费总计{{ $count['water_money'] }}元。
            <span style="color: red">合计{{ $count['electric_money'] + $count['water_money'] }}元。</span>
        </p>
        <p>
            <form  id="form">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" value="{{ $company_id }}" name="company_id"/>
            </form>
            <button class="btn btn-primary" onclick="chargeSubmit()">全部缴费</button>
        </p>
    @endif
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        function chargeSubmit(){
            var s = true;
            if (s) {
                s = false;
                maskShow();
                $.post('{{ url('company/company-utility-charge') }}', $('#form').serialize(), function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                        if (e.status) {
                            /*返回并刷新原页面*/
                            location.href = '{{ url("company/index") }}';
                        }
                    }});
                    s = true;
                }, 'json');
            }
            return false;
        }
    </script>
@endsection