<?php
/**
 * WordPress Administration Template Header
 *
 * @package WordPress
 * @subpackage Administration
 */

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if ( ! defined( 'WP_ADMIN' ) )
	require_once( dirname( __FILE__ ) . '/admin.php' );

// In case admin-header.php is included in a function.
global $title, $hook_suffix, $current_screen, $wp_locale, $pagenow, $wp_version,
	$current_site, $update_title, $total_update_count, $parent_file;

// Catch plugins that include admin-header.php before admin.php completes.
if ( empty( $current_screen ) )
	set_current_screen();

get_admin_page_title();
$title = esc_html( strip_tags( $title ) );

if ( is_network_admin() )
	$admin_title = sprintf( __('Network Admin: %s'), esc_html( $current_site->site_name ) );
elseif ( is_user_admin() )
	$admin_title = sprintf( __('Global Dashboard: %s'), esc_html( $current_site->site_name ) );
else
	$admin_title = get_bloginfo( 'name' );

if ( $admin_title == $title )
	$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), $title );
else
	$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress' ), $title, $admin_title );

/**
 * Filter the <title> content for an admin page.
 *
 * @since 3.1.0
 *
 * @param string $admin_title The page title, with extra context added.
 * @param string $title       The original page title.
 */
$admin_title = apply_filters( 'admin_title', $admin_title, $title );

wp_user_settings();

_wp_admin_html_begin();
?>
<title><?php echo $admin_title; ?></title>
<?php

wp_enqueue_style( 'colors' );
wp_enqueue_style( 'ie' );
wp_enqueue_script('utils');

$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
	pagenow = '<?php echo $current_screen->id; ?>',
	typenow = '<?php echo $current_screen->post_type; ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>
<?php

/**
 * Enqueue scripts for all admin pages.
 *
 * @since 2.8.0
 *
 * @param string $hook_suffix The current admin page.
 */
do_action( 'admin_enqueue_scripts', $hook_suffix );

/**
 * Print styles for a specific admin page based on $hook_suffix.
 *
 * @since 2.6.0
 */
do_action( "admin_print_styles-$hook_suffix" );

/**
 * Print styles for all admin pages.
 *
 * @since 2.6.0
 */
do_action( 'admin_print_styles' );

/**
 * Print scripts for a specific admin page based on $hook_suffix.
 *
 * @since 2.1.0
 */
do_action( "admin_print_scripts-$hook_suffix" );

/**
 * Print scripts for all admin pages.
 *
 * @since 2.1.0
 */
do_action( 'admin_print_scripts' );

/**
 * Fires in <head> for a specific admin page based on $hook_suffix.
 *
 * @since 2.1.0
 */
do_action( "admin_head-$hook_suffix" );

/**
 * Fires in <head> for all admin pages.
 *
 * @since 2.1.0
 */
do_action( 'admin_head' );

if ( get_user_setting('mfold') == 'f' )
	$admin_body_class .= ' folded';

if ( !get_user_setting('unfold') )
	$admin_body_class .= ' auto-fold';

if ( is_admin_bar_showing() )
	$admin_body_class .= ' admin-bar';

if ( is_rtl() )
	$admin_body_class .= ' rtl';

if ( $current_screen->post_type )
	$admin_body_class .= ' post-type-' . $current_screen->post_type;

if ( $current_screen->taxonomy )
	$admin_body_class .= ' taxonomy-' . $current_screen->taxonomy;

$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

if ( wp_is_mobile() )
	$admin_body_class .= ' mobile';

$admin_body_class .= ' no-customize-support';

?>
</head>
<?php
/**
 * Filter the admin <body> CSS classes.
 *
 * This filter differs from the post_class or body_class filters in two important ways:
 * 1. $classes is a space-separated string of class names instead of an array.
 * 2. Not all core admin classes are filterable, notably: wp-admin, wp-core-ui, and no-js cannot be removed.
 *
 * @since 2.3.0
 *
 * @param string $classes Space-separated string of CSS classes.
 */
?>
<body class="wp-admin wp-core-ui no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">
<script type="text/javascript">
	document.body.className = document.body.className.replace('no-js','js');
</script>

<script type="text/javascript">
	function category_alert(action, categories, w, h)
	{
		var titleheight = "20px";
		var bordercolor = "#464646";
		var titlecolor = "#FFFFFF";
		var titlebgcolor = "#464646";
		var bgcolor = "#F9F9F9";

		var iwidth = document.documentElement.clientWidth;
		var iheight = document.getElementById("wpwrap").scrollHeight;

		var bgObj = document.createElement("div");
		bgObj.setAttribute("id", "category_alert_background");
		bgObj.style.cssText = "position:absolute; left:0px; top:0px; width:" + iwidth + "px; height:" + Math.max(document.body.clientHeight, iheight) + "px; filter:Alpha(Opacity=50); opacity:0.5; background-color:#24140C; z-index:100;";
		document.getElementById("wpwrap").appendChild(bgObj);

		var msgObj = document.createElement("div");
		msgObj.setAttribute("id", "category_alert");
		msgObj.style.cssText = "position:absolute;font:11px; top:200px;left:" + (iwidth - w) / 2 + "px;width:" + w + "px; height:" + h + "px;text-align:center;border:1px solid" + bordercolor + "; background-color:" + bgcolor + "; padding:1px; line-height:23px; z-index:101;";

		var table = document.createElement("table");
		msgObj.appendChild(table);
		table.style.cssText = "margin:0px;border:0px;padding:0px;";
		table.cellSpacing = 0;
		var tr = table.insertRow(-1);
		var titleBar = tr.insertCell(-1);
		titleBar.style.cssText = "width:" + w + "px; height:" + titleheight + "px; text-align:center; padding:1px; margin:0px; font:bold 12px sans-serif; color:" + titlecolor + "; cursor:move; background-color:" + titlebgcolor;
		titleBar.innerHTML = "&nbsp; Did NOT select any category";

		var moveX = 0;
		var moveY = 0;
		var moveTop = 0;
		var moveLeft = 0;
		var moveable = false;
		var docMouseMoveEvent = document.onmousemove;
		var docMouseUpEvent = document.onmouseup;
		titleBar.onmousedown = function() 
		{   
			function getEvent()
			{
				return window.event || arguments.callee.caller.arguments[0];
			}

			var my_evt = getEvent();
			moveable = true;
			moveX = my_evt.clientX;
			moveY = my_evt.clientY;
			moveTop = parseInt(msgObj.style.top);
			moveLeft = parseInt(msgObj.style.left);

			document.onmousemove = function() 
			{   
				if (moveable) 
				{   
					var my_evt = getEvent();
					var x = moveLeft + my_evt.clientX - moveX;
					var y = moveTop + my_evt.clientY - moveY;
					if ( x > 0 &&( x + w < iwidth) && y > 0 && (y + h < iheight) )
					{   
						msgObj.style.left = x + "px";
						msgObj.style.top = y + "px";
					}   
				}   
			};

			document.onmouseup = function ()  
			{   
				if (moveable) 
				{   
					document.onmousemove = docMouseMoveEvent;
					document.onmouseup = docMouseUpEvent;
					moveable = false;
					moveX=0;
					moveY=0;
					moveTop=0;
					moveLeft=0;
				}
			};
		}

		var closeBtn=tr.insertCell(-1);
		closeBtn.style.cssText="cursor:pointer; padding:1px; background-color:" + titlebgcolor;
		closeBtn.innerHTML="<span style='margin-right:3px; font-size:13pt; color:" + titlecolor + ";'>x";
		closeBtn.onclick = function()
		{
			document.getElementById("wpwrap").removeChild(bgObj);
			document.getElementById("wpwrap").removeChild(msgObj);
		}

		var msgBox = table.insertRow(-1).insertCell(-1);
		msgBox.style.cssText = "font:narmol 17px sans-serif; color:black;";
		msgBox.colSpan = 2;
		link = window.location.href
		caption_style = "width:100%; height:30px; float:left; text-align:left; text-indent:10px; color:black; font-size:13px";
		form_style = "text-align:left; margin-left:9px; float:left; width:70%;";
		botton_style = "float:left;";

		var html_string = "<form action='" + link + "' method = 'post'><br/><div style=' "+ caption_style + "'>Create a New Category?</div> <div style='float:left;width:100%;'><div style='" + form_style + "'><input id='new_cate_input' type='text' style='width:100%' name='new_category_name' /></div><div style='" + botton_style + "'>&nbsp; <input type='submit' class='button button-primary' name='new_cate_submit' value='create'></div></div>";

		switch (action) {
		case "new":
			var cates = categories.replace(/^\s+|\s+$/g,'').split(/\s+/);
			var checkboxes = "<div style='float:left; text-align:left; width:100%; margin-top:16px; margin-left:9px;'><font size='4'>OR</font> select from waht you have:</div><div style='text-align:left; margin-left:12px;'>";
			for (i in cates)
				checkboxes += "<input type='checkbox' name='" + cates[i] + "'> " + cates[i] + "&nbsp;&nbsp; ";
			checkboxes += "</div><input type='submit' class='button button-primary' name='select_cate_submit' value='submit'>";

			msgBox.innerHTML = html_string + checkboxes + "</form>";
			document.getElementById("wpwrap").appendChild(msgObj);
			document.getElementById("new_cate_input").focus();
			break;

		case "choose":
			var cates = categories.replace(/^\s+|\s+$/g,'').split(/\s+/);
			var checkboxes = "<div style='float:left; text-align:left; width:100%; margin-top:16px; margin-left:9px;'><font size='4'>OR</font> select what we guess for you:</div><div style='text-align:left; margin-left:12px;'>";
			for (i in cates)
				checkboxes += "<input type='checkbox' name='" + cates[i] + "'> " + cates[i] + "&nbsp;&nbsp; ";
			checkboxes += "</div><input type='submit' class='button button-primary' name='select_cate_submit' value='submit'>";

			msgBox.innerHTML = html_string + checkboxes + "</form>";
			document.getElementById("wpwrap").appendChild(msgObj);
			document.getElementById("new_cate_input").focus();
			break;
		}
	}
</script>

<?php
// Make sure the customize body classes are correct as early as possible.
if ( current_user_can( 'edit_theme_options' ) )
	wp_customize_support_script();
?>

<div id="wpwrap">
<a tabindex="1" href="#wpbody-content" class="screen-reader-shortcut"><?php _e('Skip to main content'); ?></a>
<?php require(ABSPATH . 'wp-admin/menu-header.php'); ?>
<div id="wpcontent">

<?php
/**
 * Fires at the beginning of the content section in an admin page.
 *
 * @since 3.0.0
 */
do_action( 'in_admin_header' );
?>

<div id="wpbody">
<?php
unset($title_class, $blog_name, $total_update_count, $update_title);

$current_screen->set_parentage( $parent_file );

?>

<div id="wpbody-content" aria-label="<?php esc_attr_e('Main content'); ?>" tabindex="0">
<?php

$current_screen->render_screen_meta();

if ( is_network_admin() ) {
	/**
	 * Print network admin screen notices.
	 *
	 * @since 3.1.0
	 */
	do_action( 'network_admin_notices' );
} elseif ( is_user_admin() ) {
	/**
	 * Print user admin screen notices.
	 *
	 * @since 3.1.0
	 */
	do_action( 'user_admin_notices' );
} else {
	/**
	 * Print admin screen notices.
	 *
	 * @since 3.1.0
	 */
	do_action( 'admin_notices' );
}

/**
 * Print generic admin screen notices.
 *
 * @since 3.1.0
 */
do_action( 'all_admin_notices' );

if ( $parent_file == 'options-general.php' )
	require(ABSPATH . 'wp-admin/options-head.php');
