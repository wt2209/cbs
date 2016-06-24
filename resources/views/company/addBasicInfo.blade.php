@extends('header')

@section('title', '新公司入住')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/add.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">新公司入住 - 基本信息</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form" method="post" action="{{ url('company/store-basic-info') }}">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名称</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" name="company_name"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td colspan="2" >
                        <textarea name="company_description" class="form-control" cols="30" rows="3"></textarea>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>日常联系人</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="linkman"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>联系人电话</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="linkman_tel"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>公司负责人</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="manager"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>负责人电话</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="manager_tel"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="company_remark" class="form-control" cols="30" rows="3"></textarea>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">保存并选择房间</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ url('/js/functions.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>
    <script>
        // 联系电话(手机/电话皆可)验证
        $.validator.addMethod("isTel", function(value,element) {
            var length = value.length;
            var mobile = /^(((1[0-9]{1}))+\d{9})$/;
            var tel = /^(\d{3,4}-?)?\d{7,9}$/g;
            return this.optional(element) || tel.test(value) || (length==11 && mobile.test(value));
        }, "请正确填写您的联系方式");

        /*表单验证*/
        var s = true;
        var validate = $("#form").validate({
            debug: false, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                return true;
            },
            rules:{
                company_name:{
                    required:true,
                    maxlength:255
                },
                company_description:{
                    maxlength:255
                },
                linkman:{
                    required:true,
                    maxlength:5
                },
                linkman_tel:{
                    isTel:true
                },
                manager:{
                    maxlength:5
                },
                manager_tel:{
                    isTel:true
                },
                company_remark:{
                    maxlength:255
                }
            },
            messages:{
                company_name:{
                    required:'必须填写！',
                    maxlength:'不能多于255个字符！'
                },
                company_description:{
                    maxlength:'不能多于255个字符！'
                },
                linkman:{
                    required:'必须填写！',
                    maxlength:'不能多于5个字符！'
                },
                linkman_tel:{
                    isTel:'请填写一个正确的电话号码！'
                },
                manager:{
                    maxlength:'不能多于5个字符！'
                },
                manager_tel:{
                    isTel:'请填写一个正确的电话号码！'
                },
                company_remark:{
                    maxlength:'不能多于255个字符！'
                }
            }
        });
    </script>
@endsection