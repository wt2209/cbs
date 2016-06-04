@extends('header')

@section('title', '承包商公寓管理系统--修改公司')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改公司</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="company_id" value="{{ $company->company_id }}">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名称</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $company->company_name }}" name="company_name"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td colspan="2" >
                        <textarea name="company_description" class="form-control" cols="30" rows="3">{{ $company->company_description }}</textarea>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>日常联系人</th>
                    <td>
                        <input type="text" class="form-control input-sm" value="{{ $company->linkman }}" name="linkman"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>联系人电话</th>
                    <td>
                        <input type="text" class="form-control input-sm" value="{{ $company->linkman_tel }}" name="linkman_tel"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>公司负责人</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="manager" value="{{ $company->manager }}" />
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>负责人电话</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="manager_tel" value="{{ $company->manager_tel }}"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="company_remark" class="form-control" cols="30" rows="3">{{ $company->company_remark }}</textarea>
                    </td>
                    <td></td>
                </tr>
            </table>

            <div class="form-submit">
                <button class="btn btn-success" id="submit">提 交</button>
            </div>
        </form>

    </div>
    {{-- <div id="mask">
         <img src="{{ asset('images/load.gif') }}" width="40" alt=""/>
     </div>--}}
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ url('/js/functions.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>
    <script>
        $('input[name=add_room_type]').change(function(){
            var value = $(this).val();
            var self = $(this);
            var oRoomSelect = $('#room_select')
            var rooms = null;
            oRoomSelect.find('p').remove();
            if (value == 1) { //手动输入
                oRoomSelect.find('textarea').show();
            } else if (value == 2) {
                oRoomSelect.find('textarea').hide();
                oRoomSelect.find('label').remove();
                maskShow();
                $.get('{{ url('room/empty-room') }}', '', function(data){
                    maskHide();
                    rooms = data;
                    var str = '';
                    for (var i in rooms) {
                        str += '<label class="no-bold"><input type="checkbox" name="room_select[]" value="';
                        str += data[i]['room_name']; /*使用房间号不用id，以便与手动输入同步*/
                        str += '">&nbsp;' + data[i]['room_name'];
                        str += '</label>&nbsp;&nbsp;&nbsp;&nbsp;'
                    }
                    oRoomSelect.append('<p>' + str + '</p>');
                }, 'json')
            }
        })


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
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form

                var s = true;
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('company/store') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                /*返回并刷新原页面*/
                                location.href = "{{ url('company/index') }}";
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
                    number:'请填写一个正确的电话号码！'
                },
                manager:{
                    maxlength:'不能多于5个字符！'
                },
                manager_tel:{
                    number:'请填写一个正确的电话号码！'
                },
                company_remark:{
                    maxlength:'不能多于255个字符！'
                }
            }

        });
    </script>
@endsection