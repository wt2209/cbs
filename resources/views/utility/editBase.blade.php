@extends('header')

@section('title', '修改水电底数')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/utility/editBase.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改水电底数</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('utility/base') }}"><< 返回底数页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="u_base_id" value="{{ $utilityBase->u_base_id }}"/>
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">房间号</th>
                    <td width="20%">
                        {{ $utilityBase->building }}-{{ $utilityBase->room_number }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>年份</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $utilityBase->year }}" name="year"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>月份</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $utilityBase->month }}" name="month"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>电表数</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $utilityBase->electric_base }}" name="electric_base"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>水表数</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $utilityBase->water_base }}" name="water_base"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>抄表人</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" value="{{ $utilityBase->recorder }}" name="recorder"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td colspan="2" width="30%">
                        <textarea name="u_base_remark" class="form-control" cols="30" rows="3">{{ $utilityBase->u_base_remark }}</textarea>
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
                    $.post('{{ url('utility/update-base') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                room_number:{
                    required:true,
                    number:true,
                    min:1,
                    max:65535
                },
                building:{
                    required:true
                }
            },
            messages:{
                room_number:{
                    required:'失败：必须填写房间号！',
                    number:'失败：房间号必须是一个数字！',
                    max:'失败：房间号必须小于65535',
                    min:'失败：房间号必须大于1'
                },
                building:{
                    required:'必须填写！'
                }
            }

        });
    </script>
@endsection