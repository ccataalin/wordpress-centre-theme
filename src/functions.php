<?php
function uncode_language_setup()
{
  load_child_theme_textdomain('uncode', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'uncode_language_setup');

function uncode_hdx_api_init(){
  add_settings_field('hdx-mixpanel-token', 'HDX Mixpanel Token', 'render_hdx_mixpanel_token', 'general');
  add_settings_field('hdx-google-analytics-token', 'HDX Google Analytics Token', 'render_hdx_google_analytics_token', 'general');
  // Register our setting so that $_POST handling is done for us and
  // our callback function just has to echo the <input>
  register_setting( 'general', 'hdx-mixpanel-token' );
  register_setting( 'general', 'hdx-google-analytics-token' );
}
add_action( 'admin_init', 'uncode_hdx_api_init' );

function render_hdx_mixpanel_token() {
    echo '<input name="hdx-mixpanel-token" id="hdx-mixpanel-token" type="text" value="' . get_option('hdx-mixpanel-token') . '" class="code" />';
}
function render_hdx_google_analytics_token() {
    echo '<input name="hdx-google-analytics-token" id="hdx-google-analytics-token" type="text" value="' . get_option('hdx-google-analytics-token') . '" class="code" />';
}

function theme_enqueue_styles()
{
  $production_mode = ot_get_option('_uncode_production');
  $resources_version = ($production_mode === 'on') ? null : rand();
  $parent_style = 'uncode-style';
  $child_style = array('uncode-custom-style');
  wp_enqueue_style($parent_style, get_template_directory_uri() . '/library/css/style.css', array(), $resources_version);
  wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', $child_style, $resources_version);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function custom_javascript()
{
  wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/humdata-footer.js', array('jquery'), false, true);
}
add_filter('wp_enqueue_scripts','custom_javascript');


add_filter( 'wp_mail_from', function() {
  return 'wordpress@[MyDomain]';
} );

//override parent theme partials
require_once( get_stylesheet_directory(). '/partials/elements.php' );
require_once( get_stylesheet_directory(). '/partials/headers.php' );


//get first image in post content
function catch_that_image($postid) {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field('post_content', $postid), $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ //Defines a default image
    $first_img = get_stylesheet_directory_uri() . '/assets/default.svg';
  }
  return $first_img;
}


//get first link in post content
function get_content_link( $content = false )
{
if ( ! in_the_loop() )
  return;

  // allows using this function also for excerpts
  ! $content AND $content = get_the_content();

  $content = preg_match_all( '/href\s*=\s*[\"\']([^\"\']+)/', $content, $links );
  $content = $links[1][0];
  //$content = make_clickable( $content );

  return $content;
}


//add extra contact fields to user profiles
function user_contact($contactmethods) 
{
  $contactmethods['position'] = 'Position';
  $contactmethods['twitter'] = 'Twitter';
  $contactmethods['facebook'] = 'Facebook';
  $contactmethods['linkedin'] = 'LinkedIn';
  return $contactmethods;
}
add_filter('user_contactmethods','user_contact',10,1);


//shortcodes
function blockquote($att, $content = null) 
{
  extract(shortcode_atts(array(
    'author' => '',
  ), $att));
  if ($author !== '') {
    $auth_str = '<br><span>-'.$author.'</span>'; 
  }
  else {
    $auth_str = '';
  }
  $str = '<blockquote>“'.$content.'”'.$auth_str.'</blockquote>';
  return $str;
}
add_shortcode('blockquote', 'blockquote');


function quote($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
  ), $att));
  $str = '<div class="label">'.$label.'</div><p>'.$content.'</p>';
  return $str;
}
add_shortcode('quote', 'quote');


function casestudy($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
    'link' => '',
  ), $att));
  $str = '<a href="'.$link.'"><p><span class="label">'.$label.'</span><br>'.$content.'</p></a>';
  return $str;
}
add_shortcode('casestudy', 'casestudy');


function media($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
    'link' => '',
  ), $att));
  $str = '<a href="'.$link.'"><p><span class="label">'.$label.'</span>'.$content.'</p></a>';
  return $str;
}
add_shortcode('media', 'media');


function dataviz($att, $content = null) 
{
  extract(shortcode_atts(array(
    'title' => '',
    'author' => '',
    'link' => '',
  ), $att));
  if ($author != '') $auth_str = '<p class="author">'.$author.'</p>';
  else $auth_str = '';
  $str = '<a href="'.$link.'"><p class="title">'.$title.'</p>'.$auth_str.'</a>';
  return $str;
}
add_shortcode('dataviz', 'dataviz');


function tweet($att, $content = null) 
{
  extract(shortcode_atts(array(
    'author' => '',
    'link' => '',
  ), $att));
  $str = '<a href="'.$link.'" target="_blank"><p class="tweet-text">“'.$content.'”</p><p class="author">@'.$author.'<br></p><i class="fa fa-twitter twhite" aria-hidden="true"></i></a>';
  return $str;
}
add_shortcode('tweet', 'tweet');


function video($att, $content = null) 
{
  extract(shortcode_atts(array(
      'src' => ''
  ), $att));
  $str = '<div class="content videoplayer preview"><video muted autoplay loop><source src="'.$src.'" type="video/mp4">Your browser does not support the video tag.</video><div class="btn playpause-btn"></div></div>';
  return $str;
}
add_shortcode('video', 'video');
