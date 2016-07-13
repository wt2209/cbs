@extends('header')

@section('title', '修改备注')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改备注</a></li>
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
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="punish_remark" class="form-control" cols="30" rows="3">{{ $punish->punish_remark }}</textarea>
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
                    $.post('{{ url('punish/update-remark') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                /*返回并刷新原页面*/
                                location.href = document.referrer;
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
                punish_remark:{
                    maxlength:255
                }
            },
            messages:{
                punish_id:{
                    required:"非法!",
                    number:"非法！"
                },
                punish_remark:{
                    maxlength:"备注不能超过255个字符！"
                }
            }
        });
    </script>
@endsection