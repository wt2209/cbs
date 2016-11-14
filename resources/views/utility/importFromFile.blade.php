@extends('header')
@section('title', '从文件导入')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/utility/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">从文件导入</a></li>
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
    <div class="bg-success" style="padding: 10px;font-size: 14px;"><strong style="color: red">请注意：</strong><br>
        1.支持xls、xlsx格式<br>
        2.确保EXCEL文件A-D列依次为：“房间号”、“电表底数”、“水表底数”、“备注”<br>
        3.确保第一行是标题行，系统将从第二行数据开始导入
    </div>

@endsection
@section('content')
    <div class="table-responsive">
        <form id="form" method="post" enctype="multipart/form-data" action="{{ url('utility/import-base-file') }}">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="year">
            <input type="hidden" name="month">
            <input type="hidden" name="recorder">
            <input type="hidden" name="record_time">
            <input type="file" style="margin:20px 0 20px 0;" name="import_file" >
            <button type="submit" class="btn btn-success">提交</button>
        </form>
    </div>

@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/utility/add.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>

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
            }
        });
    </script>
@endsection