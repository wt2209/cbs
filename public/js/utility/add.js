$(function(){

    //添加行
    $('#addRows').click(function(){
        var iRowNumber = 5;
        //console.log($('.table tr:last').attr('_num'))
        var iCurrentNumber = parseInt($('.table tr:last').attr('_num'));
        if (!iCurrentNumber) {
            alert(2)
            return false;
        }
        var oTable= $('.table');
        var iTotalNumber = iRowNumber + iCurrentNumber
        for (var i = iCurrentNumber+1; i <= iTotalNumber; i++) {
            var sTr = '<tr _num="'+ i +'">';
            sTr += '<td><input type="text" class="form-control input-sm" name="'+ i +'[room]" placeholder="房间号"/></td>';
            sTr += '<td><input type="text" class="form-control input-sm"  name="'+ i +'[electric_base]" placeholder="电表底数"/></td>';
            sTr += '<td><input type="text" class="form-control input-sm" name="'+ i +'[water_base]" placeholder="水表底数"/></td>';
            sTr += '<td><input type="text" class="form-control input-sm" name="'+ i +'[u_base_remark]" placeholder="备注"/></td>';
            sTr += '</tr>';
            oTable.append(sTr);
        }
    })
})