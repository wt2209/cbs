@extends('header')
@section('title', '新增房间')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/room/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">新增房间</a></li>
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
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">房间名</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" name="room_name"
                               placeholder="例如：10101"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>类型</th>
                    <td>
                        <select name="room_type" class="form-control">
                            <option value="1" selected>居住用房</option>
                            <option value="2">餐厅</option>
                            <option value="3">服务用房</option>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="room_remark" class="form-control" cols="30" rows="3"></textarea>
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
                        popdown({'message':e.message, 'status': e.status});
                        if (e.status) {
                            $('#form')[0].reset();
                        }
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                room_name:{
                    required:true
                }
            },
            messages:{
                room_name:{
                    required:'失败：必须填写房间号！'
                }
            }
        });
    </script>
@endsection