@extends('header')

@section('title', '修改底数')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改底数</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company-log/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="cl_id" value="{{ $companyLog->cl_id}}"/>
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">房间号</th>
                    <td width="20%">
                        {{ $companyLog->room->room_name }}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>变动类型</th>
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
                    <td></td>
                </tr>
                <tr>
                    <th>变动时间</th>
                    <td>
                        {{substr($companyLog->created_at, 0, 10)}}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>电表底数</th>
                    <td>
                        <input type="text" class="form-control input-sm" value="{{ $companyLog->electric_base }}" name="electric_base"/>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>水表底数</th>
                    <td>
                        <input type="text" class="form-control input-sm" value="{{ $companyLog->water_base }}" name="water_base"/>
                    </td>
                    <td></td>
                </tr>
            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">提 交</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
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
                    $.post('{{ url('company-log/edit-base') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            }
        });
    </script>
@endsection