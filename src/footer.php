<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package uncode
 */

global $metabox_data, $is_redirect, $menutype;

$limit_width = $limit_content_width = $footer_content = $footer_text_content = $footer_icons = $footer_full_width = '';
$alignArray = array('left','right');

$general_style = ot_get_option('_uncode_general_style');

$footer_last_style = ot_get_option( '_uncode_footer_last_style');
$footer_last_bg = ot_get_option('_uncode_footer_bg_color');
$footer_last_bg = ($footer_last_bg == '') ? ' style-'.$footer_last_style.'-bg' : ' style-'.$footer_last_bg.'-bg';

$post_type = isset( $post->post_type ) ? $post->post_type : 'post';
if (is_archive() || is_home()) $post_type .= '_index';
if (is_404()) $post_type = '404';
if (is_search()) $post_type = 'search_index';

/** Get page width info **/
if (isset($metabox_data['_uncode_specific_footer_width'][0]) && $metabox_data['_uncode_specific_footer_width'][0] !== '') {
	if ($metabox_data['_uncode_specific_footer_width'][0] === 'full') $footer_full_width = true;
	else $footer_full_width = false;
} else {
	$footer_generic_width = ot_get_option( '_uncode_'.$post_type.'_footer_width');
	if ($footer_generic_width !== '') {
		if ($footer_generic_width === 'full') $footer_full_width = true;
		else $footer_full_width = false;
	}
	else
	{
		$footer_full = ot_get_option( '_uncode_footer_full');
		$footer_full_width = ($footer_full !== 'on') ? false : true;
	}
}
if (!$footer_full_width) $limit_content_width = ' limit-width';

if (isset($metabox_data['_uncode_specific_footer_block'][0]) && $metabox_data['_uncode_specific_footer_block'][0] !== '') {
	$footer_block = $metabox_data['_uncode_specific_footer_block'][0];
} else {
	$footer_block = ot_get_option('_uncode_' . $post_type . '_footer_block');
	if ($footer_block === '' && $footer_block !== 'none') {
		$footer_block = ot_get_option('_uncode_footer_block');
	}
}

// if (isset($footer_block) && !empty($footer_block) && $footer_block !== 'none' && defined( 'WPB_VC_VERSION' )) {
// 	$footer_block = apply_filters( 'wpml_object_id', $footer_block, 'post' );
// 	$footer_block_content = get_post_field('post_content', $footer_block);
// 	if ($footer_full_width) {
// 		$footer_block_content = preg_replace('#\s(unlock_row)="([^"]+)"#', ' unlock_row="yes"', $footer_block_content);
// 		$footer_block_content = preg_replace('#\s(unlock_row_content)="([^"]+)"#', ' unlock_row_content="yes"', $footer_block_content);
// 		$footer_block_counter = substr_count($footer_block_content, 'unlock_row_content');
// 		if ($footer_block_counter === 0) $footer_block_content = str_replace('[vc_row ', '[vc_row unlock_row="yes" unlock_row_content="yes" ', $footer_block_content);
// 	} else {
// 		$footer_block_content = preg_replace('#\s(unlock_row)="([^"]+)"#', ' unlock_row="yes"', $footer_block_content);
// 		$footer_block_content = preg_replace('#\s(unlock_row_content)="([^"]+)"#', ' unlock_row_content="no"', $footer_block_content);
// 		$footer_block_counter = substr_count($footer_block_content, 'unlock_row_content');
// 		if ($footer_block_counter === 0) $footer_block_content = str_replace('[vc_row ', '[vc_row unlock_row="yes" unlock_row_content="no" ', $footer_block_content);
// 	}
// 	$footer_content .= uncode_remove_wpautop($footer_block_content);
// }



$footer_position = ot_get_option('_uncode_footer_position');
if ($footer_position === '') $footer_position = 'left';

$footer_copyright = ot_get_option('_uncode_footer_copyright');
if ($footer_copyright !== 'off') {
	$footer_text_content = '&copy; '.date("Y").' '.get_bloginfo('name') . ' ' . esc_html__('All rights reserved','uncode');
}

$footer_text = ot_get_option('_uncode_footer_text');
if ($footer_text !== '' && $footer_copyright === 'off') {
	$footer_text_content = uncode_the_content($footer_text);
}

//build footer menus
$secondary_list = uncode_get_menu_list('secondary');
//$primary_list = uncode_get_menu_list('primary');

$menu_items = wp_get_nav_menu_items( 'expand-menu' );
$menu_list .= '<ul id="menu-primary">';
foreach( $menu_items as $menu_item ) {
	$menu_list .= '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
}
$menu_list .= '</ul>';


if ($footer_text_content !== '') {
	$copyright = $footer_text_content;
	$footer_text_content = '<div class="site-info uncell col-lg-3 pos-middle text-'.$footer_position.'">'.$menu_list.'</div><div class="site-info uncell col-lg-5 pos-middle text-'.$footer_position.'">'.$secondary_list.'</div><div class="site-info uncell col-lg-4 pos-middle text-'.$footer_position.'"><h2>Stay in touch</h2>
					<p>Sign up to our mailing list for latest updates on the Centre</p><div id="mc_embed_signup">
							<form action="//humdata.us14.list-manage.com/subscribe/post?u=ea3f905d50ea939780139789d&amp;id=99796325d1" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
							    <div id="mc_embed_signup_scroll">
									<div class="mc-field-group">
										<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Your email address"><input type="submit" value="submit" name="subscribe" id="mc-embedded-subscribe" class="btn submit-btn">
									</div>
									
									<div id="mce-responses" class="clear">
										<div class="response" id="mce-error-response" style="display:none"></div>
										<div class="response" id="mce-success-response" style="display:none"></div>
									</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
								    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_ea3f905d50ea939780139789d_99796325d1" tabindex="-1" value=""></div>
							    </div>
							</form>
							<p>or download (<a href="https://centre.humdata.org/wp-content/uploads/centreforhumdata_handout_dec2016.pdf" target="_blank">EN</a> | <a href="https://centre.humdata.org/wp-content/uploads/2017/06/CentreForHumdata_Flyer_01_2017_FR_v2.pdf" target="_blank">FR</a> | <a href="https://centre.humdata.org/wp-content/uploads/2017/06/DataCentre_One_Pager_06_2017_ES_v2.pdf" target="_blank">ES</a>) our brochure to learn more</p>
						</div></div><!-- site info -->';
}

$footer_social = ot_get_option('_uncode_footer_social');
if ($footer_social !== 'off') {
	$socials = ot_get_option( '_uncode_social_list','',false,true);
	if (isset($socials) && !empty($socials) && count($socials) > 0) {
		foreach ($socials as $social) {
			if ($social['_uncode_social'] === '') continue;
			$footer_icons .= '<div class="social-icon icon-box icon-box-top icon-inline"><a href="'.esc_url($social['_uncode_link']).'" target="_blank"><i class="'.esc_attr($social['_uncode_social']).'"></i></a></div>';
		}
	}
}

if ($footer_icons !== '') $footer_icons = '<div class="uncell col-lg-6 pos-middle text-'.($footer_position === 'center' ? $footer_position : $alignArray[!array_search($footer_position, $alignArray)]).'">' . $footer_icons . '</div>';

if (($footer_text_content !== '' || $footer_icons !== '')) {
	switch ($footer_position) {
		case 'left':
			$footer_text_content = $footer_text_content . $footer_icons;
			break;
		case 'center':
			$footer_last_bg .= ' footer-center';
			$footer_text_content = $footer_icons . $footer_text_content;
			break;
		case 'right':
			$footer_text_content = $footer_icons . $footer_text_content;
			break;
	}
	$footer_last_bg .= ' footer-last';
	if (strpos($menutype ,'vmenu') !== false) $footer_last_bg .= ' desktop-hidden';
	$footer_content .= uncode_get_row_template($footer_text_content, $limit_width, $limit_content_width, $footer_last_style, $footer_last_bg, false, false, false);
}?>
							</div><!-- sections container -->
						</div><!-- page wrapper -->
					<?php if ($is_redirect !== true) : ?>


			<?php if (is_front_page()) { ?>
				<section id="contact" class="bg-pattern-green">
					<h2>Stay in touch</h2>
					<p class="large">Sign up to our mailing list to get the latest updates on the Centre or download (<a href="https://centre.humdata.org/wp-content/uploads/centreforhumdata_handout_dec2016.pdf" target="_blank">EN</a> | <a href="https://centre.humdata.org/wp-content/uploads/2017/06/CentreForHumdata_Flyer_01_2017_FR_v2.pdf" target="_blank">FR</a> | <a href="https://centre.humdata.org/wp-content/uploads/2017/06/DataCentre_One_Pager_06_2017_ES_v2.pdf" target="_blank">ES</a>) our brochure to learn more</p>
	
						<!-- Begin MailChimp Signup Form -->
						<div id="mc_embed_signup">
							<form action="//humdata.us14.list-manage.com/subscribe/post?u=ea3f905d50ea939780139789d&amp;id=99796325d1" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
							    <div id="mc_embed_signup_scroll">
									<div class="mc-field-group">
										<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Your email address"><input type="submit" value="submit" name="subscribe" id="mc-embedded-subscribe" class="btn submit-btn">
									</div>
									
									<div id="mce-responses" class="clear">
										<div class="response" id="mce-error-response" style="display:none"></div>
										<div class="response" id="mce-success-response" style="display:none"></div>
									</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
								    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_ea3f905d50ea939780139789d_99796325d1" tabindex="-1" value=""></div>
							    </div>
							</form>
						</div>
						<!--End mc_embed_signup-->
						
				</section>
			<?php } ?>

					<footer id="colophon" class="site-footer">
						<?php
							if (function_exists('qtranxf_getLanguage')) $footer_content = __($footer_content);
							echo $footer_content;
						?>
					</footer>
					<?php endif; ?>
				</div><!-- main container -->
			</div><!-- main wrapper -->
		</div><!-- box container -->
	</div><!-- box wrapper -->
	<?php
	$footer_uparrow = ot_get_option('_uncode_footer_uparrow');
	if (wp_is_mobile()) {
		$footer_uparrow_mobile = ot_get_option('_uncode_footer_uparrow_mobile');
		if ($footer_uparrow_mobile === 'off') $footer_uparrow = 'off';
	}
	if ($footer_uparrow !== 'off') {
		$scroll_higher = '';
		if (strpos($menutype ,'vmenu') === false) {
			if ($limit_content_width === '') $scroll_higher = ' footer-scroll-higher';
		}
		echo '<div class="style-light footer-scroll-top'.$scroll_higher.'"><a href="#" class="scroll-top"><i class="fa fa-angle-up fa-stack fa-rounded btn-default btn-hover-nobg"></i></a></div>';
	}
	$vertical = (strpos($menutype, 'vmenu') !== false || $menutype === 'menu-overlay') ? true : false;
	if (!$vertical) {

		$search_animation = ot_get_option('_uncode_menu_search_animation');
		if ($search_animation === '' || $search_animation === '3d') $search_animation = 'contentscale';

	?>

	<div class="slideshow-modal-overlay">
		<span class="close">&times;</span>
		<div class="slideshow-modal">
			<a class="slideshow-btn prev" data-dir="prev">&#10094;</a>
    		<a class="slideshow-btn next" data-dir="next">&#10095;</a>
    		<div class="slides"></div>
    	</div>
	</div>

	<div class="overlay overlay-<?php echo $search_animation; ?> style-dark style-dark-bg overlay-search" data-area="search" data-container="box-container">
		<div class="mmb-container"><div class="menu-close-search mobile-menu-button menu-button-offcanvas mobile-menu-button-dark lines-button x2 overlay-close close" data-area="search" data-container="box-container"><span class="lines"></span></div></div>
		<div class="search-container"><?php get_search_form( true ); ?></div>
	</div>

	<?php if (is_front_page()) { ?>
	<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
	<?php } ?>

	<?php }

	wp_footer(); ?>
</body>
</html>