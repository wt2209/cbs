@extends('header')
@section('title', '开具罚单')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/punish/create.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">开具罚单</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('punish/uncharged-list') }}"><< 返回未缴费列表页</a>
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="warning-message">
            <span style="color:red">请注意：</span>一旦开具，罚单将不能修改，且必须注明原因后才能撤销！
        </div>
    </nav>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名称</th>
                    <td width="20%">
                        {{ $company->company_name }}
                        <input type="hidden" name="company_id" value="{{ $company->company_id }}">
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>罚款金额</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="money"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>处罚原因</th>
                    <td colspan="2" width="30%">
                        <textarea name="reason" class="form-control" cols="30" rows="3"></textarea>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>开单时间</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="created_at"
                               placeholder="例如：2016-6-1"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="punish_remark" class="form-control" cols="30" rows="3"></textarea>
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
                    $.post('{{ url('punish/store') }}', $('#form').serialize(), function(e){
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
                money:{
                    required:true,
                    number:true
                },
                reason:{
                    required:true,
                    maxlength:255
                },
                created_at:{
                    date:true
                },
                punish_remark:{
                    maxlength:255
                }
            },
            messages:{
                money:{
                    required:"金额必须填写！",
                    number:"请填写正确的数额！"
                },
                reason:{
                    required:"原因必须填写！",
                    maxlength:"原因不得多于255个字符！"
                },
                created_at:{
                    date:"请填写正确的日期格式，或者不填！"
                },
                punish_remark:{
                    maxlength:"备注不得多于255个字符！"
                }
            }
        });
    </script>
@endsection