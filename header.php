<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">



<head profile="http://www.w3.org/2005/10/profile">

	<?php include_once("configure.inc") ?>

	

	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<meta name="author" content="<?=$meta["author"]?>" />

	<meta name="language" content="<?=$meta["language"]?>" />

	<meta name="distribution" content="<?=$meta["distribution"]?>" />

	<meta name="robots" content="<?=$meta["robots"]?>" />

	<meta name="revisit-after" content="<?=$meta["revisit-after"]?>" /> 



	<meta name="title" content="<?=$meta["title"]?>" />

	<meta name="keywords" content="<?=$meta["keywords"]?>" />

	<meta name="description" content="<?=$meta["description"]?>" />



	<title><?=$meta["title"]?></title>

	

	<link rel="stylesheet" type="text/css" media="all" href="reset.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="timberline.css" />

	<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="ie.css" /><![endif]-->

	

	<script type="text/javascript" src="jquery-1.3.2.js"></script>

	<?=$headCode?>	

</head>



<body>

<!--[if lt IE 7]>

<div id="ie6Warning">

	<h2>Time to upgrade your browser</h2>

	<p>If you're reading this, you're surfing using Internet Explorer 6, an eight-year-old browser that cannot cope with the demands of the modern internet. For the best web experience, we strongly recommend upgrading to <a href="http://www.getfirefox.com/">Firefox</a>, <a href="http://www.opera.com/">Opera</a>, <a href="http://www.apple.com/safari/">Safari</a>, <a href="http://www.google.com/chrome">Google Chrome</a>, or a more recent version of <a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx">Internet Explorer</a>.</p>

</div>

<![endif]-->

<div id="container">

	<div id="header">

		<div id="navigation">

			<ul>

				<li><a href="."<?=navMatch("home")?>>Home</a></li>

				<li><a href="dallas-lawn-care.php"<?=navMatch("lawncare")?>>Lawn Care</a></li>

				<li><a href="dallas-landscaping.php"<?=navMatch("landscaping")?>>Landscaping</a></li>

				<li><a href="gallery.php"<?=navMatch("gallery")?>>Gallery</a></li>

				<li><a href="about.php"<?=navMatch("about")?>>About Us</a></li>

			</ul>

		</div>

<?php if( $navMatch !="tos"){?>

		<h1 id="logo" class="imaged"><span>Timberline Lawn and Landscape</span></h1>

		<h2 id="paragraph_style_2">No <br />complicated <br />estimates!</h2>

		<h3 id="callToAction" class="clearer"><?=$cta?></h3>

<?php }?>
	</div>