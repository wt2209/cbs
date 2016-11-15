@extends('header')

@section('title', '修改密码')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改密码</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('user/users') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">用户名</th>
                    <td width="20%">
                        {{ $user->user_name }}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="10%">原密码</th>
                    <td width="20%">
                        <input type="password" class="form-control input-sm" name="password"/>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="10%">新密码</th>
                    <td width="20%">
                        <input type="password" class="form-control input-sm" name="new_password"/>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th width="10%">确认密码</th>
                    <td width="20%">
                        <input type="password" class="form-control input-sm" name="confirm_password"/>
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
        /*表单验证*/
        var s = true;
        var validate = $("#form").validate({
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                var s = true;
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('user/change-password') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                /*返回并刷新原页面*/
                                location.href = "{{ url('user/users') }}";
                            }
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            }
        });
    </script>
@endsection