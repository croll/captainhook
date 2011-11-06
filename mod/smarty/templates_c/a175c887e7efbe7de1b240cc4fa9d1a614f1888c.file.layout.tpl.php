<?php /* Smarty version Smarty-3.0.8, created on 2011-11-06 04:31:21
         compiled from "/home/web/captainhook/mod/smarty/../../mod/site_test/hooks_templates/layout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20973927794eb5ff898fcd15-90805968%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a175c887e7efbe7de1b240cc4fa9d1a614f1888c' => 
    array (
      0 => '/home/web/captainhook/mod/smarty/../../mod/site_test/hooks_templates/layout.tpl',
      1 => 1320549953,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20973927794eb5ff898fcd15-90805968',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/home/web/captainhook/mod/smarty/smarty/source/plugins/modifier.escape.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('title')->value);?>
</title>
    <link rel="shortcut icon" href="<?php echo $_smarty_tpl->getVariable('favicon')->value;?>
" type="image/x-icon" />
  </head>
  <body>
    <?php echo \mod\smarty\Main::smartyFunction_hook(array('mod'=>'site_test','name'=>'banner'),$_smarty_tpl);?>

    <?php echo \mod\smarty\Main::smartyFunction_hook(array('mod'=>'site_test','name'=>'content'),$_smarty_tpl);?>

    <?php echo \mod\smarty\Main::smartyFunction_hook(array('mod'=>'site_test','name'=>'footer'),$_smarty_tpl);?>

  </body>
</html>
