<?php
return array (
  'app' => 'Admin',
  'model' => 'xitong',
  'action' => 'default',
  'data' => '',
  'type' => '1',
  'status' => '1',
  'name' => '系统管理',
  'icon' => '',
  'remark' => '',
  'listorder' => '0',
  'children' => 
  array (
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'city',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '开通城市',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'daima',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '违章代码库',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'jilu',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '违章记录库',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'log',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '违章记录库修改日志',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'select',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '查询状态码',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'role',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '角色管理',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'user',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '管理员用户',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'window',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '服务商窗口',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Xitong',
      'action' => 'shuju',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '数据字典',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Setting',
      'action' => 'userdefault',
      'data' => '',
      'type' => '0',
      'status' => '1',
      'name' => '个人信息',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin',
          'model' => 'Setting',
          'action' => 'password',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '修改密码',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
          'children' => 
          array (
            array (
              'app' => 'Admin',
              'model' => 'Setting',
              'action' => 'password_post',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '提交修改',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
          ),
        ),
        array (
          'app' => 'Admin',
          'model' => 'User',
          'action' => 'userinfo',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '修改信息',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
          'children' => 
          array (
            array (
              'app' => 'Admin',
              'model' => 'User',
              'action' => 'userinfo_post',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '修改信息提交',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
          ),
        ),
      ),
    ),
  ),
);