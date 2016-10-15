@extends('header')

@section('title', $roleName . ' 权限修改')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">{{ $roleName }} 权限修改</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('user/roles') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <p style="height:20px;"></p>
        <form id="form">
            @foreach($allPermissions as $permission)
                <p style="display: inline-block;width: 120px;">
                @if (in_array($permission->id, $rolePermissionIds))
                    <label class="no-bold"><input type="checkbox" name="permission_id[]" checked="" value="{{ $permission->id }}">{{ $permission->display_name }}</label>
                @else
                    <label class="no-bold"><input type="checkbox" name="permission_id[]" value="{{ $permission->id }}">{{ $permission->display_name }}</label>
                @endif
                </p>
            @endforeach
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
                    $.post('{{ url('room/update') }}', $('#form').serialize(), function(e){
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