@extends('header')

@section('title', '修改房间')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/room/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改房间</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('room/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="room_id" value="{{ $room->room_id }}"/>
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">房间号</th>
                    <td width="20%">
                        {{ $room->building.'-'.$room->room_number }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="room_remark" class="form-control" cols="30" rows="3">{{ $room->room_remark }}</textarea>
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
    <script src="{{ url('/js/functions.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>
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
                    $.post('{{ url('room/store') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }

                return false;
            },
            rules:{
                room_number:{
                    required:true,
                    number:true,
                    min:1,
                    max:65535
                },
                building:{
                    required:true
                }
            },
            messages:{
                room_number:{
                    required:'失败：必须填写房间号！',
                    number:'失败：房间号必须是一个数字！',
                    max:'失败：房间号必须小于65535',
                    min:'失败：房间号必须大于1'
                },
                building:{
                    required:'必须填写！'
                }
            }

        });
    </script>
@endsection