@extends('header')

@section('title', '公司详情')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/add.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">{{ $companyDetail['name'] }} - 详情</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名称</th>
                    <td width="20%">
                        {{ $companyDetail['name'] }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td colspan="2" >
                        {{ $companyDetail['description'] }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>入住时间</th>
                    <td colspan="2" >
                        {{ substr($companyDetail['created_at'], 0, 10) }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>日常联系人</th>
                    <td>
                        {{ $companyDetail['link'] }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>联系人电话</th>
                    <td>
                        {{ $companyDetail['link_tel'] }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>公司负责人</th>
                    <td>
                        {{ $companyDetail['manager'] }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>负责人电话</th>
                    <td>
                        {{ $companyDetail['manager_tel'] }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        {{ $companyDetail['remark'] }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>占用房间</th>
                    <td colspan="4">
                        共占用 <span style="color:red">{{ $companyDetail['count']['livingRoomNumber'] }}</span> 个居住房间，
                        共计 <span style="color:red">{{ $companyDetail['count']['livingPersonNumber'] }}</span> 人次，其中：<br>
                        @foreach($companyDetail['livingRoom'] as $k => $v)
                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $k }}人间({{ $companyDetail['count'][$k] }}个)：
                            <p style="padding-left: 35px;">{{$v}}</p>
                        @endforeach
                        共占用 <span style="color:red">{{ $companyDetail['count']['diningRoomNumber'] }}</span> 个餐厅：<br>
                            <p style="padding-left: 30px;">{{ $companyDetail['diningRoom'] }}</p>
                        共占用 <span style="color:red">{{ $companyDetail['count']['serviceRoomNumber'] }}</span> 个服务用房：<br>
                        <p style="padding-left: 30px;">{{ $companyDetail['serviceRoom'] }}</span> </p>
                    </td>
                </tr>
            </table>
            <div class="form-submit">
                <a href="{{ url('company/index') }}" class="btn btn-success" >返回</a>
            </div>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ url('/js/functions.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>
@endsection