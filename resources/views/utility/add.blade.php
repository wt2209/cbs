@extends('header')
@section('title', '录入底数')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/utility/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">录入底数</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('utility/base') }}"><< 返回底数页</a>
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        年份：
                        <input type="text" id="year" class="form-control input-sm">&nbsp;&nbsp;&nbsp;
                        月份：
                        <input type="text" id="month" class="form-control input-sm">&nbsp;&nbsp;&nbsp;
                        抄表人：
                        <input type="text" id="recorder" class="form-control input-sm">&nbsp;&nbsp;&nbsp;
                        抄表时间：
                        <input type="text" id="record_time" class="form-control input-sm" placeholder="格式：2015-8-20">&nbsp;&nbsp;&nbsp;
                    </div>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button id="addRows" type="submit" class="btn btn-primary">添加栏位</button>&nbsp;&nbsp;&nbsp;或者&nbsp;&nbsp;&nbsp;
        <button class="btn btn-primary btn-sm">从文件导入</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="year">
            <input type="hidden" name="month">
            <input type="hidden" name="recorder">
            <input type="hidden" name="record_time">
            <table class="table table-bordered table-hover table-condensed">
                <thead>
                <tr class="active">
                    <th  width="15%">房间号</th>
                    <th  width="15%">电表底数</th>
                    <th  width="15%">水表底数</th>
                    <th>备注</th>
                </tr>
                </thead>
                <tr id="trBase" _num="1">
                    <td>
                        <input type="text" class="form-control input-sm" name="1[room]">
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="1[electric_base]">
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="1[water_base]">
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="1[u_base_remark]">
                    </td>
                </tr>
            </table>
            <button type="submit" class="btn btn-success">提交</button>
            <div style="height:20px"></div>
        </form>
    </div>

@endsection
@section('bottom')
    <p></p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/utility/add.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>

    <script>
        /*表单验证*/
        var validate = $("#form").validate({
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                var oForm = $('#form');
                oForm.children('input[name=year]').val($('#year').val());
                oForm.children('input[name=month]').val($('#month').val());
                oForm.children('input[name=recorder]').val($('#recorder').val());
                oForm.children('input[name=record_time]').val($('#record_time').val());
                var s = true;
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('utility/store') }}', oForm.serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                /*返回并刷新原页面*/
                                location.href = '{{ url("utility/add") }}';
                            }
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
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