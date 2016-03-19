@extends('header')

@section('title', '调整房间')


@section('css')
    <link rel="stylesheet" href="{{ url('/css/company/changeRooms.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">调整房间</a></li>
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
                        {{ $company->company_name }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>选择房间</th>
                    <td colspan="3">
                        <label class="no-bold"><input type="radio" name="add_room_type" value="1" checked=""/>手动输入</label>&nbsp;&nbsp;&nbsp;
                        <label class="no-bold"><input type="radio" name="add_room_type" value="2" />从空房间选择</label>&nbsp;&nbsp;&nbsp;
                        <div id="room_select">
                            <textarea name="room_input" class="form-control"
                                      placeholder="每个房间之间用空格间隔，例如：1-1101 2-1113。将自动过滤掉不存在和非空的房间"></textarea>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="form-submit">
                <input type="hidden" name="company_id" value="{{$company->company_id}}"/>
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
        //旧房间
        var hiddenStr = '<div style="visibility:hidden">';
        @foreach($rooms as $room)
            hiddenStr += '<input type="hidden" name="old_rooms[]" value="';
            hiddenStr += "{{ $room->building.'-'.$room->room_number }}";
            hiddenStr += '"/>';
        @endforeach
        hiddenStr += '</div>';
        $('.form-submit').append(hiddenStr);

        //手动输入房间号与自动获取房间号切换
        $('input[name=add_room_type]').change(function(){
            var value = $(this).val();
            $('#room_select').find('p').remove();
            if (value == 1) { //手动输入
                setManualRoom();
            } else if (value == 2) {
                setAutoRoom();
            }
        })

        //开始时执行手动输入房间号
        setManualRoom();

        //表单提交
        $('#submit').click(function(){   //表单提交句柄,为一回调函数，带一个参数：form
            var s = true;
            if (s) {
                s = false;
                maskShow();
                $.post('{{ url('company/change-rooms-store') }}', $('#form').serialize(), function(e){
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
        })


        //手动输入房间号
        function setManualRoom(){
            var oRoomSelect = $('#room_select');
            var str = '';
            @foreach($rooms as $room)
                str += '{{$room->building.'-'.$room->room_number}} '; /*使用房间号不用id，以便与手动输入同步*/
            @endforeach
            oRoomSelect.find('textarea').html('').show().append(str);
        }

        //自动获取房间号
        function setAutoRoom(){
            var oRoomSelect = $('#room_select');
            var str = '';
            @foreach($rooms as $room)
                str += '<label class="no-bold"><input type="checkbox" checked name="room_select[]" value="';
                str += '{{$room->building.'-'.$room->room_number}}'; /*使用房间号不用id，以便与手动输入同步*/
                str += '">&nbsp;' + '{{ $room->building.'-'.$room->room_number }}';
                str += '</label>&nbsp;&nbsp;&nbsp;&nbsp;';
            @endforeach
            oRoomSelect.find('textarea').hide();
            oRoomSelect.find('label').remove();
            maskShow();
            $.get('{{ url('room/empty-room') }}', '', function(data){
                maskHide();
                for (var i in data) {
                    str += '<label class="no-bold"><input type="checkbox" name="room_select[]" value="';
                    str += data[i]['room_name']; /*使用房间号不用id，以便与手动输入同步*/
                    str += '">&nbsp;' + data[i]['room_name'];
                    str += '</label>&nbsp;&nbsp;&nbsp;&nbsp;'
                }
                if (data.length == 0) {
                    str += '<span style="color:#666"> 没有多余空房间了...</span>';
                }
                oRoomSelect.append('<p>' + str +'</p>');
            }, 'json')
        }


    </script>
@endsection