@extends('header')

@section('title', '月报表')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">月报表</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('sheet/index') }}" class="refresh"></a>
    </div>

@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>文件名</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach($files as $file)
                <tr>
                    <td>
                        {{ $file }}
                    </td>
                    <td>
                        <a href="{{ url('sheet/download/'.$file) }}" class="btn btn-info btn-xs download" >下载</a>
                    </td>
                </tr>
            @endforeach
        </table>
        <script>
            $('.download').click(function(){
                maskShow();
                setTimeout(maskHide,800);
                return true;
            })
        </script>
{{--        {!! $companies->appends([
                'company_name'=>isset($_GET['company_name']) ? $_GET['company_name'] : '',
                'person_name'=>isset($_GET['person_name']) ? $_GET['person_name'] : ''
            ])->render() !!}--}}
    </div>
    @endsection
    @section('modal')
            <!-- delete modal -->
    <div id="delete-modal" class="modal fade bs-example-modal-sm">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">删除确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要删除吗？
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="delete-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <div class="function-area">
        <button class="btn btn-success btn-sm" id="createButton">生成本日报表</button>
        <script>
            $('#createButton').click(function(){
                maskShow();
                $.get('{{ url('sheet/create') }}', '', function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                        /*返回并刷新原页面*/
                        location.reload();
                    }});
                    s = true;
                }, 'json');
            })


        </script>
    </div>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection