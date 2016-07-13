@extends('header')
@section('title', '系统配置项')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">系统配置项</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="150"></th>
                    <th width="200">值</th>
                    <th>说明</th>
                </tr>
                <tr>
                    <th>分页条数</th>
                    <td>
                        <input  class="form-control" type="text" name="page_number" value="{{ $pageNumber }}">
                    </td>
                    <td>
                        分页情况下每页显示的条数
                    </td>
                </tr>
                <tr>
                    <th>水电费精度</th>
                    <td>
                        <input  class="form-control" type="text" name="precision" value="{{ $precision }}">
                    </td>
                    <td>
                        水电费小数点后的位数
                    </td>
                </tr>
                <tr>
                    <th>电费单价</th>
                    <td>
                        <input  class="form-control" type="text" name="electric_money" value="{{ $electricMoney }}">
                    </td>
                    <td>
                        单位：元
                    </td>
                </tr>
                <tr>
                    <th>水费单价</th>
                    <td>
                        <input  class="form-control" type="text" name="water_money" value="{{ $waterMoney }}">
                    </td>
                    <td>
                        单位：元
                    </td>
                </tr>
            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">修 改</button>
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
                    $.post('{{ url('config/store') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status,'callback':function(){
                            if (e.status) {
                                window.location.reload();
                            }
                            s = true;
                        }});

                    }, 'json');
                }
                return false;
            },
            rules:{
                page_number:{
                    required:true,
                    digits:true
                },
                precision:{
                    required:true,
                    digits:true
                },
                electric_money:{
                    required:true,
                    number:true
                },
                water_money:{
                    required:true,
                    number:true
                }
            },
            messages:{
                page_number:{
                    required:'必须填写分页条数！',
                    digits:'必须是整数！'
                },
                precision:{
                    required:'必须填写精度！',
                    digits:'必须是整数！'
                },
                electric_money:{
                    required:'必须填写电费单价！',
                    number:'必须是数字！'
                },
                water_money:{
                    required:'必须填写水费单价！',
                    number:'必须是数字！'
                }
            }
        });
    </script>
@endsection