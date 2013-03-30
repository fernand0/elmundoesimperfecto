<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo PUBWICH_TITLE?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="generator" content="<?php echo PUBWICH_NAME;?> <?php echo PUBWICH_VERSION;?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1"> 
		<link rel="stylesheet" media="screen" href="<?php echo Pubwich::getThemeUrl()?>/static/style.css" type="text/css">
		<link type="text/plain" rel="author" href="humans.txt" />
<?php echo Pubwich::getHeader() ?>
	</head>
	<body>
		<div id="wrap">
			<h1><?php echo PUBWICH_TITLE?></h1>
			<hr>
			<div class="clearfix">

<?php echo Pubwich::getLoop()?>

			</div>
			<div id="footer">
				<div class="footer-inner">
					<hr>
					<?php echo sprintf( Pubwich::_('Fetched %s, proudly aggregated by %s.'), date('Y'), '<a class="pubwich" href="'.PUBWICH_WEB.'">'.PUBWICH_NAME.'</a>'  )?>
				</div>
			</div>
		</div>
<?php echo Pubwich::getFooter() ?>
	</body>
</html>
