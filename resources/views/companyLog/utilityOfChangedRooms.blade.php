@extends('header')
@section('title', '变动房间水电底数')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/utility/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">变动房间水电底数</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>


@endsection
@section('content')
    @if (count($companyLogs) == 0)
        <div class="bg-success" style="padding: 10px;font-size: 14px;margin-bottom: 10px;">
            没有需要录入的水电底数，点击“完成”返回公司明细：
        </div>
        <a href="{{ url('company/index') }}" class="btn btn-success">完成</a>
    @else
        <div class="bg-success" style="padding: 10px;font-size: 14px;margin-bottom: 10px;">
            以下房间发生变动且未录入变动时的水电底数，请录入：
        </div>
        <div class="table-responsive">
            <form id="form">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <table class="table table-bordered table-hover table-condensed">
                    <thead>
                    <tr class="active">
                        <th>房间号</th>
                        <th>所属公司</th>
                        <th>变动类型</th>
                        <th>变动时间</th>
                        <th>变动时电表底数</th>
                        <th>变动时水表底数</th>
                    </tr>
                    </thead>
                    @foreach($companyLogs as $companyLog)
                        <tr>
                            <td>
                                {{ $companyLog->room->room_name }}
                            </td>
                            <td>
                                {{ $companyLog->company->company_name}}
                            </td>
                            <td>
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
                            </td>
                            <td>{{substr($companyLog->created_at, 0, 10)}}</td>
                            <td>
                                <input type="text" class="form-control input-sm" name="{{$companyLog->cl_id}}[electric_base]">
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" name="{{$companyLog->cl_id}}[water_base]">
                            </td>
                        </tr>
                    @endforeach
                </table>
                <button type="submit" id="submit" class="btn btn-success">提交</button>
            </form>
        </div>
    @endif

@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/utility/add.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        var s = true;
        var validate = $("#form").validate({
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('company-log/utility-of-changed-rooms') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status});
                        if (e.status) {
                            /*返回并刷新原页面*/
                            location.href = '{{ url("company/index") }}';
                        }
                        s = true;
                    }, 'json');
                }
                return false;
            }
        });

    </script>
@endsection