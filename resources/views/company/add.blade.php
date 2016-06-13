@extends('header')

@section('title', '新公司入住')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/add.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">新公司入住</a></li>
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

                <tr>
                    <th>选择房间</th>
                    <td colspan="2" style="border-right: 1px #ddd solid" id="living">
                        居住用房：<br>
                        {{--<div class="col-lg-2">
                            <div class="input-group">
                                <label class="input-group-addon">
                                    <input type="checkbox" name="living[_1][room_id]" value="111">&nbsp;101010
                                 </label>
                                <select class="form-control" name="living[_1][person_number]">
                                    <option value="2">2人间</option>
                                    <option value="4">4人间</option>
                                    <option value="6">6人间</option>
                                    <option value="8">8人间</option>
                                </select>
                                <span class="input-group-addon">
                                    <label class="no-bold"><input type="radio" value="1" name="living[_1][gender]">男</label>&nbsp;
                                    <label class="no-bold"><input type="radio" value="2" name="living[_1][gender]">女</label>
                                </span>
                            </div>
                        </div>--}}

                    </td>
                    <td width="25%" style="border-right: 1px #ddd solid " id="dining">
                        餐厅用房：<br>
                            <label class="no-bold">
                                <input type="checkbox" name="dining[room_id][]" value="222">101010
                            </label>
                    </td>
                    <td  id="service">
                        服务用房：<br>
                        <label class="no-bold">
                            <input type="checkbox" name="service[room_id][]" value="333">101010
                        </label>
                    </td>
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
        $(function(){
            $.get('{{ url('room/all-empty-room') }}', '', function(data){
                var livingStr = '';
                for (var i in data['living']) {
                    var current = data['living'][i]
                    livingStr += '<div class="col-lg-2">';
                    livingStr += '<div class="input-group">';
                    livingStr += '<label class="input-group-addon">';
                    livingStr += '<input type="checkbox" name="living[' + current['room_id'] + '][room_id]" value="'+current['room_id']+'">&nbsp;'+current['room_name'];
                    livingStr += '</label>';
                    livingStr += '<select class="form-control" name="living[' + current['room_id'] + '][person_number]">';
                    livingStr += '<option value="2">2人间</option>;'
                    livingStr += '<option value="4">4人间</option>';
                    livingStr += '<option value="6">6人间</option>';
                    livingStr += '<option value="8">8人间</option>';
                    livingStr += '</select>';
                    livingStr += '<span class="input-group-addon">';
                    livingStr += '<label class="no-bold"><input type="radio" value="1" name="living[' + current['room_id'] + '][gender]">男</label>&nbsp;';
                    livingStr += '<label class="no-bold"><input type="radio" value="2" name="living[' + current['room_id'] + '][gender]">女</label>';
                    livingStr += '</span>';
                    livingStr += '</div>';
                    livingStr += '</div>';

                }
                $('#living').html(livingStr);
            }, 'json')
        })

/*

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
                    var str = '';
                    for (var i in data) {
                        str += '<label class="no-bold"><input type="checkbox" name="room_select[]" value="';
                        str += data[i]['room_name']; /!*使用房间号不用id，以便与手动输入同步*!/
                        str += '">&nbsp;' + data[i]['room_name'];
                        str += '</label>&nbsp;&nbsp;&nbsp;&nbsp;'
                    }
                    if (data.length == 0) {
                        str += '<span style="color:#666"> 没有多余空房间了...</span>';
                    }
                    oRoomSelect.append('<p>' + str + '</p>');
                }, 'json')
            }
        })*/


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
                                location.href = '{{ url("company/index") }}';
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