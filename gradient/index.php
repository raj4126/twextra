<?php
if (isset($_POST['gradtype']) == false) 
    $gradtype = 'top';
else
   $gradtype = $_POST['gradtype'];

if (isset($_POST['size']) == false) 
{
	$size = 100;
}
else
{
	$size = $_POST['size'];
}

if($gradtype == 'top')
	{
		$height = $size;
		$width = 10;
	}
	else
	{
		$height = 100;
		$width = $size;

	}

if (isset($_POST['c1']) == false) 
    $c1 = '#3E3A70';
else
	$c1 = $_POST['c1'];

if (isset($_POST['c2']) == false) 
    $c2 = '#71A4D1';
else
	$c2 = $_POST['c2'];

	$newc1 = str_replace('#', '', $c1);
	$newc2 = str_replace('#', '', $c2);	

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>Gradient Generator</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" href="js_color_picker_v2.css" media="screen">


<script type="text/javascript" src="color_functions.js"></script>
<script type="text/javascript" src="js_color_picker_v2.js"></script>


<style>


</style>

</head>
<body>
    <div id="wrap">
	    <div id="top_content">
	        <div id="header">											
						<!-- topheader -->
						<div id="topheader">
							<h1 id="title">
								<a href="index.php">Gradient Generator</a>
							</h1>
						</div>

							<div id="adheader">			
			<script type="text/javascript"><!--
			google_ad_client = "pub-5980555758755094";
			/* 468x60, created 8/26/08 */
			google_ad_slot = "5223857588";
			google_ad_width = 468;
			google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>	
			</div>
							
						<!-- navigation -->
						<div id="navigation">
							
						</div>
						
				</div>
				<!-- header ends here -->

				<div id="content">
					<br><br><br>
					<h1> Create a background gradient graphic </h1>

					<form action="index.php" method="post">

		<table border="0" cellspacing="10" cellpadding="0">		
		<tr><td>Style:</td>
		<td>
		<select name="gradtype" id="gradtype" >
						<?
						if($gradtype == 'top')
							echo '<option value="top" selected="selected" >Vertical Gradient (Top to Bottom)</option>';
						else
							echo '<option value="top" >Vertical Gradient (Top to Bottom)</option>';

						if($gradtype == 'left')
							echo '<option value="left" selected="selected">Horizontal Gradient (Left to Right)</option>';
						else
							echo '<option value="left">Horizontal Gradient (Left to Right)</option>';
						?>
					</select>
		</td></tr>
		<tr><td>Color 1</td>
		<td><input type="text" style="width: 6em;" name="c1" class="color" id="c1" value="<? echo $c1 ?>"  />
<a href="javascript:;" onclick="showColorPicker(this,document.forms[0].c1)"><img src="img/color.png" alt="Color Picker" width="16" height="16" border="0" title="Pick a color"></a>
		</td></tr>
		<tr><td>Color 2</td>
		<td><input type="text" style="width: 6em;" name="c2" id="c2" class="color" value="<? echo $c2 ?>"  />
<a href="javascript:;" onclick="showColorPicker(this,document.forms[0].c2)"><img src="img/color.png" alt="Color Picker" width="16" height="16" border="0" title="Pick a color"></a>

		</td></tr>
		<tr><td>Size:</td>
		<td><input type="text" style="width: 3em;" id="s" name="size" value="<? echo $size ?>"  />

		</td></tr>
		<tr><td colspan=2><input type="submit" class="button" name="update" value="Create Gradient"  /></td></tr>		
	</table>
	<br><br>
	
</form>
																							
		
<br><br>

<?
$url = 'grad.php?type='.$gradtype.'&width='.$width.'&height='.$height.'&start_colour='.$newc1.'&end_colour='.$newc2;
?>

<? if($gradtype == 'top') { ?>
<div style="margin: auto; border: 1px solid black; background: url(<? echo $url ?>) repeat-x; width: 90%; height: <? echo $height ?>px;"></div>
<? } else { ?>
<div style="margin: auto; border: 1px solid black; background: url(<? echo $url ?>) repeat-y; height: 200px; width: <? echo $width ?>px;"></div>

<? } ?>

<br><br>

				</div>

	    </div>
			<!-- here ends header + content ( topcontent ) -->
			
	    <!-- footer -->
			<div id="footer">
			<div id="footer_bg">
			
				<!-- right bottom link -->
				<div id="design">
					Powered by <a href="http://www.phpemporium.com">PHP Emporium</a>
				</div>
				
			
			
			</div>
			</div>
</div>
</body>
</html>
