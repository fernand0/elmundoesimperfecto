<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo PUBWICH_TITLE?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="generator" content="<?php echo PUBWICH_NAME;?> <?php echo PUBWICH_VERSION;?>" />
        <meta name="viewport" content="width=device-width"> 
		<link rel="stylesheet" media="screen" href="<?php echo Pubwich::getThemeUrl()?>/static/style.css" type="text/css">
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
					<p>
					    <?php echo sprintf( Pubwich::_('Fetched %s, proudly aggregated by %s.'), date('Y'), '<a class="pubwich" href="'.PUBWICH_WEB.'">'.PUBWICH_NAME.'</a>'  )?>
				    </p>
				</div>
			</div>
		</div>
        <?php echo Pubwich::getFooter() ?>
	</body>
</html>
