@extends('header')
@section('title', '撤销罚单')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/common.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">撤销罚单</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('punish/uncharged-list') }}"><< 返回未缴费列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="punish_id" value="{{ $punish->punish_id }}">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名称</th>
                    <td width="20%">
                        {{ $punish->company->company_name }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th width="10%">罚款原因</th>
                    <td width="20%">
                        {{ $punish->reason }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th width="10%">金额</th>
                    <td width="20%">
                        {{ $punish->money }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>撤销原因</th>
                    <td colspan="2" width="30%">
                        <textarea name="cancel_reason" class="form-control" cols="30" rows="3"></textarea>
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
                    $.post('{{ url('punish/update-cancel') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                /*返回并刷新原页面*/
                                location.href = "{{ url('punish/uncharged-list') }}";
                            }
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                punish_id:{
                    required:true,
                    number:true
                },
                cancel_reason:{
                    required:true,
                    maxlength:255
                }
            },
            messages:{
                punish_id:{
                    required:"非法!",
                    number:"非法！"
                },
                cancel_reason:{
                    required:"必须填写！",
                    maxlength:"不能超过255个字符！"
                }
            }
        });
    </script>
@endsection