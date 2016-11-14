@extends('header')
@section('title', '创建角色')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/add.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">创建角色</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('user/roles') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form" method="post" action="{{ url('user/create-role') }}">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">角色名</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" name="role_name"
                               placeholder="例如：管理员"/>
                        @if ($errors->has('createRoleFailed'))
                            <span class="help-block">
                                        <strong style="color: red">{{ $errors->first('createRoleFailed') }}</strong>
                                    </span>
                        @endif
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td>
                        <textarea name="role_description" class="form-control" cols="30" rows="3"></textarea>
                    </td>
                </tr>
                <td></td>
            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">下一步</button>
            </div>
        </form>
    </div>
@endsection
@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
@endsection