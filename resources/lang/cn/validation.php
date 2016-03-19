<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'room_id'=>[
            'integer'=>'失败：房间ID必须是一个整数！',
            'min'=>'失败：房间ID格式错误！'
        ],
        'building' => [
            'required' => '失败：必须填写楼号！',
        ],
        'room_number' => [
            'required' => '失败：必须填写房间号！',
            'integer'=>'失败：房间号必须是一个数字！',
            'max'=>'失败：房间号必须小于65535',
            'min'=>'失败：房间号必须大于1'
        ],
        'company_id' => [
            'integer'=>'失败：公司ID必须是一个整数！',
            'min'=>'失败：公司ID格式错误！'
        ],
        'company_name'=>[
            'required'=>'失败：必须填写公司名称！',
            'between'=>'失败：公司名称不得多于255个字符！'
        ],
        'company_description'=>[
            'between'=>'失败：公司描述不得多于255个字符！'
        ],
        'linkman'=>[
            'required'=>'失败：必须填写日常联系人姓名！',
            'between'=>'失败：请填写正确的日常联系人姓名！'
        ],
        'linkman_tel'=>[
            'numeric'=>'失败：请填写正确的联系人电话！'
        ],
        'manager'=>[
            'between'=>'失败：请填写正确的负责人姓名！'
        ],
        'manager_tel'=>[
            'numeric'=>'失败：请填写正确的负责人电话！'
        ],
        'company_remark'=>[
            'between'=>'失败：备注不得多于255个字符！'
        ]
    ],


];
