<?php

// Sanitize params
function filter_enum($var, $enum, $default = null)
{
    return in_array($var, $enum) ? $var : $default;
}

$site = filter_enum($_REQUEST['site'], ['facebook', 'google', 'twitter'], 'facebook');
$cover = filter_var($_REQUEST['cover'], FILTER_VALIDATE_URL) or die('Invalid cover url');
$avatar = isset($_REQUEST['avatar']) ? filter_var($_REQUEST['avatar'], FILTER_VALIDATE_URL) : false;

$special = isset($_REQUEST['special']) && $_REQUEST['special'] ? 1 : 0;
$specialDesign = isset($_REQUEST['special_design']) && $_REQUEST['special_design'] ? 1 : 0;

$siteUrl = 'http://www.korko.fr/clevercover/';

$css = [
    'style',
    'cover_style',
    'pageguide.min',
    'smoothness/jquery-ui-1.8.23.custom',
];
$js = [
    'StackBlur',
    'jquery-1.8.0.min',
    'jquery-ui-1.8.23.min',
    'jquery.support',
    'jquery.drag',
    'jquery.identify',
    'pageguide.min',
    'toolbox',
    'popup',
    'cover_script',
];

?>
<!DOCTYPE html>
<!--
This script can generate from a global picture two parts in order to be used as cover and profile picture in Facebook.
----------------------------------------------------------------------------
"THE BEER-WARE LICENSE" (Revision 42):
<jeremy.lemesle@korko.fr> wrote this file. As long as you retain this notice you
can do whatever you want with this stuff. If we meet some day, and you think
this stuff is worth it, you can buy me a beer in return. Jeremy Lemesle
----------------------------------------------------------------------------
-->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
        foreach ($css as $style) {
            $path = 'media/css/'.$style.'.css';
            $stats = stat($path);
            echo '<link rel="stylesheet" href="'.$path.'?mtime='.($stats['mtime']).'" />';
        }
        foreach ($js as $script) {
            $path = 'media/js/'.$script.'.js';
            $stats = stat($path);
            echo '<script type="text/javascript" src="'.$path.'?mtime='.($stats['mtime']).'"></script>';
        }
?>
		<title>CleverCover</title>
	</head>
	<body>
		<div id="bluebar">
			<div class="social-network facebook">
				<div id="fb-root"></div>
				<div class="fb-like" data-href="<?= $siteUrl ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
				<script type="text/javascript">asyncjs('https://connect.facebook.net/fr_FR/all.js#xfbml=1', 'facebook-jssdk');</script>
			</div>
			<div class="social-network google">
				<div class="g-plusone" data-annotation="none" data-href="<?= $siteUrl ?>"></div>
				<script type="text/javascript">asyncjs('https://apis.google.com/js/plusone.js');</script>
			</div>
			<div class="social-network twitter">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= $siteUrl ?>" data-text="CleverCover" data-via="korkof" data-related="korkof">Tweet</a>
				<script type="text/javascript">asyncjs('https://platform.twitter.com/widgets.js', 'twitter-wjs');</script>
			</div>

			<div id="save" class="button">Save</div>
		</div>
		<div id="content">
			<div id="content_inner">
				<canvas id="canvas_cover"></canvas>
				<canvas id="canvas_picture"></canvas>
			</div>
			<div>
				<div id="cover_choice">
					<label><input type="radio" name="cover_choice" value="cover" checked="checked" />Cover</label>
					<label><input type="radio" name="cover_choice" value="avatar" />Avatar</label>
				</div>
				<div><label>Change Ratio <input type="range" name="cover_ratio" min="0" max="100" value="100" step="1" /></label></div>
				<div><label>Reverse? <input type="checkbox" name="cover_flip" /></label></div>
				<div><label>Blur <input type="range" name="cover_blur" min="0" max="100" value="0" step="1" /></label></div>
				<div><label>Color mask <input type="color" name="color_mask" /></label><input type="range" name="color_mask_opacity" min="0" max="100" value="0" step="1" /></div>
			</div>
		</div>
		<div id="comments">
			<p>Why not leave a message for other users or the developer?</p>
			<div class="fb-comments" data-href="<?= $siteUrl ?>" data-num-posts="2" data-width="470"></div>
		</div>
		<div id="popup">
			<div id="popup_header"><a href="#" onclick="popup.close(); return false;">X</a></div>
			<h1>CleverCover</h1><div id="popup_content"></div>
		</div>
		<div id="overlay"></div>
		<ul id="tlyPageGuide" data-tourtitle="Help?">
			<li class="tlypageguide_left" data-tourtarget="#content_inner">
	  			<div>
					Here is your picture, you can click and drag to move it.
	  			</div>
			</li>
			<li class="tlypageguide_left" data-tourtarget="#cover_slider">
				<div>
					Feel free to resize your picture from the cover width to your picture's.
				</div>
			</li>
			<li class="tlypageguide_bottom" data-tourtarget="#cover_flip">
				<div>
					If you want to, you can flip your picture horizontaly (left becomes right).
				</div>
			</li>
			<li class="tlypageguide_bottom" data-tourtarget="#save">
				<div>
					Once your picture is ready, click here to save your pictures.
				</div>
			</li>
			<li class="tlypageguide_top" data-tourtarget="#comments">
				<div>
					Do not hesitate to leave a comment if you like or not this tool.
				</div>
			</li>
		</ul>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				popup.content('<p>Preparing your cover... It may take a while.', false);
				tl.pg.init();
				var site='<?= $site ?>',
				    cover='<?= $cover ?>',
				    avatar=<?php echo !$avatar ? 'null' : "'".$avatar."'"; ?>,
				    special=<?= $special ?>,
				    special_design=<?= $specialDesign ?>;
				cleverCover.init(site, cover, avatar, function(success) {
					popup.close();
					if(!success) {
						window.location.href = location.href.substr(0, location.href.lastIndexOf('/')+1)+'?site='+site+'&special='+special+'&special_design='+special_design;
					}
				}, special, special_design);
			});
		</script>
	</body>
</html>
