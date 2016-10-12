@extends('header')
@section('title', '房间类型')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">房间类型</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('company/search') }}">
                   <p class="warning-message">注意：修改完成后，系统将会启用修改后的房间类型计算费用。</p>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:;">添加新项</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="150">房间人数</th>
                    <th width="200">月租金</th>
                    <th>操作</th>
                </tr>
                @foreach($rentTypes as $rentType)
                    <tr>
                        <td>
                            <input class="form-control" type="text" name="person_number[]" value="{{ $rentType->person_number }}">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="rent_money[]" value="{{ $rentType->rent_money }}">
                        </td>
                        <td>
                            <button  delete_id="{{ $rentType->rent_type_id }}" class="btn btn-danger btn-xs delete-button">退租</button>
                        </td>
                    </tr>
                @endforeach
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
            focusInvalid: false, //当为false时，验证无效
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('config/...') }}', $('#form').serialize(), function(e){
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
                person_number:{
                    required:true,
                    digits:true
                },
                rent_money:{
                    required:true,
                    number:true
                }
            },
            messages:{
                person_number:{
                    required:'必须填写！',
                    digits:'必须是整数！'
                },
                rent_money:{
                    required:'必须填写！',
                    number:'必须是数字！'
                }
            }
        });
    </script>
@endsection