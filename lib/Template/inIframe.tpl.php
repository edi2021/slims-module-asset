<!doctype html>
<html>
<head><title><?= $page_title??''; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" /><meta http-equiv="Expires" content="Sat, 26 Jul 1997 05:00:00 GMT" />
<link rel="stylesheet" type="text/css" href="<?php echo SWB.'css/bootstrap.min.css'; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo SWB.'css/core.css?'; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo JWB; ?>chosen/chosen.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SWB.'admin/'.$sysconf['admin_template']['css']; ?>?<?= date('this') ?>" />
<?php if (isset($css)) { echo $css; } ?>
<style type="text/css">
  body { background: #FFFFFF; }
</style>
<?php if (isset($js)) { echo $js; } ?>
</head>
<body>
<div id="pageContent">
<?php echo $content; ?>
</div>
</body>
</html>
