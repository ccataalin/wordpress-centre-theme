<?php
function uncode_language_setup()
{
  load_child_theme_textdomain('uncode', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'uncode_language_setup');

function uncode_hdx_api_init(){
  add_settings_field('hdx-mixpanel-token-prod', 'HDX Mixpanel Token Production', 'render_hdx_mixpanel_token_prod', 'general');
  add_settings_field('hdx-mixpanel-token-stage', 'HDX Mixpanel Token Stage', 'render_hdx_mixpanel_token_stage', 'general');
  add_settings_field('hdx-mixpanel-token-local', 'HDX Mixpanel Token Local', 'render_hdx_mixpanel_token_local', 'general');
  add_settings_field('hdx-google-analytics-token', 'HDX Google Analytics Token', 'render_hdx_google_analytics_token', 'general');
  // Register our setting so that $_POST handling is done for us and
  // our callback function just has to echo the <input>
  register_setting( 'general', 'hdx-mixpanel-token-prod' );
  register_setting( 'general', 'hdx-mixpanel-token-stage' );
  register_setting( 'general', 'hdx-mixpanel-token-local' );
  register_setting( 'general', 'hdx-google-analytics-token' );
}
add_action( 'admin_init', 'uncode_hdx_api_init' );

function render_hdx_mixpanel_token_prod() {
    echo '<input name="hdx-mixpanel-token-prod" id="hdx-mixpanel-token-prod" type="text" value="' . get_option('hdx-mixpanel-token-prod') . '" class="code" />';
}
function render_hdx_mixpanel_token_stage() {
    echo '<input name="hdx-mixpanel-token-stage" id="hdx-mixpanel-token-stage" type="text" value="' . get_option('hdx-mixpanel-token-stage') . '" class="code" />';
}
function render_hdx_mixpanel_token_local() {
    echo '<input name="hdx-mixpanel-token-local" id="hdx-mixpanel-token-local" type="text" value="' . get_option('hdx-mixpanel-token-local') . '" class="code" />';
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
  wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', $child_style, filemtime( get_stylesheet_directory() . '/style.css' ) );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');


function custom_javascript()
{
  wp_dequeue_script('uncode-app');
  //wp_enqueue_script('uncode-app-mod', get_stylesheet_directory_uri() . '/js/theme-app-modified.js', array('jquery'), false, true);
  wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/humdata-footer.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/humdata-footer.js' ), true);
}
add_action('wp_enqueue_scripts','custom_javascript', 100); //lower the priority of the script inclusion -> so our scripts and styles can override


//override parent theme partials
require_once( get_stylesheet_directory(). '/partials/elements.php' );
require_once( get_stylesheet_directory(). '/partials/headers.php' );
//require_once( get_stylesheet_directory(). '/partials/menus.php' );


//get first image in post content
function catch_that_image($postid) {
  $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'single-post-thumbnail' )[0];
  if ($image == '') {
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
  else {
    return $image;
  }
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

//get section elements in post content
function get_content_sections( $content = false )
{
if ( ! in_the_loop() )
  return;

  // allows using this function also for excerpts
  ! $content AND $content = get_the_content();
  
  $dom = new DOMDocument();
  //suppress php dom warnings on html5 tags https://stackoverflow.com/questions/6090667/php-domdocument-errors-warnings-on-html5-tags
  libxml_use_internal_errors(true); 
  $dom->loadHTML($content);
  $sections = $dom->getElementsByTagName('section');
  libxml_clear_errors();

  return $sections;
}

add_filter('the_content', function( $content ){
    //--Remove all inline styles--
    $content = preg_replace('/ style=("|\')(.*?)("|\')/','',$content);
    return $content;
}, 20);


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
    'author' => ''
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
    'label' => ''
  ), $att));
  $str = '<div class="label">'.$label.'</div><p>'.$content.'</p>';
  return $str;
}
add_shortcode('quote', 'quote');


function casestudy($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
    'link' => ''
  ), $att));
  $str = '<a href="'.$link.'" target="_blank"><p><span class="label">'.$label.'</span><br>'.$content.'</p></a>';
  return $str;
}
add_shortcode('casestudy', 'casestudy');


function media($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
    'link' => ''
  ), $att));
  $str = '<a href="'.$link.'" target="_blank"><p><span class="label">'.$label.'</span>'.$content.'</p></a>';
  return $str;
}
add_shortcode('media', 'media');

function articles($att, $content = null) 
{
  extract(shortcode_atts(array(
    'label' => '',
    'link' => ''
  ), $att));
  $str = '<a href="'.$link.'" target="_blank"><p><span class="label">'.$label.'</span>'.$content.'</p></a>';
  return $str;
}
add_shortcode('articles', 'articles');


function dataviz($att, $content = null) 
{
  extract(shortcode_atts(array(
    'title' => '',
    'author' => '',
    'link' => ''
  ), $att));
  if ($author != '') $auth_str = '<p class="author">'.$author.'</p>';
  else $auth_str = '';
  $str = '<a href="'.$link.'" target="_blank"><p class="title">'.$title.'</p>'.$auth_str.'</a>';
  return $str;
}
add_shortcode('dataviz', 'dataviz');


function announcement($att, $content = null) 
{
  extract(shortcode_atts(array(
    'title' => '',
    'author' => '',
    'link' => ''
  ), $att));
  if ($author != '') $auth_str = '<p class="author">'.$author.'</p>';
  else $auth_str = '';
  $str = '<a href="'.$link.'" target="_blank"><p class="title">'.$title.'</p>'.$auth_str.'</a>';
  return $str;
}
add_shortcode('announcement', 'announcement');


function impact($att, $content = null) 
{
  extract(shortcode_atts(array(
    'title' => '',
    'author' => '',
    'link' => ''
  ), $att));
  if ($author != '') $auth_str = '<p class="author">'.$author.'</p>';
  else $auth_str = '';
  $str = '<a href="'.$link.'"><p class="title">'.$title.'</p>'.$auth_str.'</a>';
  return $str;
}
add_shortcode('impact', 'impact');


function tweet($att, $content = null) 
{
  extract(shortcode_atts(array(
    'author' => '',
    'link' => ''
  ), $att));
  $str = '<a href="'.$link.'" target="_blank"><p class="tweet-text">“'.$content.'”</p><p class="author">@'.$author.'<br></p><i class="fa fa-twitter twhite" aria-hidden="true"></i></a>';
  return $str;
}
add_shortcode('tweet', 'tweet');


function video($att, $content = null)
{
  extract(shortcode_atts(array(
    'src' => '',
    'id' => ''
  ), $att));
  $uniqid = 'video-' . uniqid();
  $str = '<iframe id="'.$uniqid.'" src="'.$src.'?rel=0&showinfo=0&controls=0&enablejsapi=1" frameborder="0" allowfullscreen></iframe>';
  return $str;
}
add_shortcode('video', 'video');


function gallery($att, $content = null)
{
  extract(shortcode_atts(array(
    'name' => '',
    'ids' => ''
  ), $att));
  $image_ids = explode(',', strval($ids));

  $str = '<div class="slideshow-container" id="'.$name.'">';
  $count = 0;
  foreach ($image_ids as $id) {
    $img = image_downsize( $id, 'slideshow-img');

    // if returned image is not an intermediate size (ie full size), default to large size image
    $img = (!$img[3]) ? wp_get_attachment_image_src( $id, 'large')[0] : $img[0];
    $str = $str .  '<img data-index="' . $count . '" src="' . $img . '">';
    $count++;
  }
  $str = $str . '</div>';
  return $str;
}
add_shortcode('gallery', 'gallery');


function quicktip($att, $content = null)
{
  extract(shortcode_atts(array(
    'title' => ''
  ), $att));
  $content = ($content != strip_tags($content)) ? $content : '<p>'.$content.'</p>';
  $str = '<div class="quick-tip-container"><h5>'.$title.':</h5>'.$content.'</div>';
  return $str;
}
add_shortcode('quicktip', 'quicktip');


function code($att, $content = null)
{
  extract(shortcode_atts(array(
    'lang' => ''
  ), $att));
  $content = str_replace('<br>', '', $content);
  $content = preg_replace("#<br />#", "", $content);
  $str = '<pre><code>'.$content.'</code></pre>';
  return $str;
}
add_shortcode('code', 'code');

// exclude pages from search results
function searchfilter($query) {
  if ($query->is_search && !is_admin()) { //&& isset($_GET['post_type'])
      $query->set('post_type',array($_GET['post_type']));

      //fix weird bug with ampersands in search term (only works if treated as separate characters)
      $searchterm = str_replace('&', ' & ', get_query_var('s'));
      set_query_var('s', $searchterm);
  }
}
add_filter('pre_get_posts', 'searchfilter');


function custom_template($template){
  if( !is_single() )
    return $template;
  global $wp_query;
  $c_template = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
  return empty( $c_template ) ? $template : $c_template;
}

add_filter( 'template_include', 'custom_template' );

function get_custom_templates(){
  add_post_type_support( 'post', 'page-attributes' );
}

/** Add custom size to support larger size images for slideshows **/
add_theme_support( 'post-thumbnails' );
add_image_size( 'slideshow-img', 1600 );


/**
 * Select target _blank by default.
 *
 * Outputs javascript that hooks into the WordPress link dialog
 * and sets the target _blank checkbox to be selected by default.
 *
 * @return null
 */
function default_target_blank() {
  ?>
  <script>
    jQuery(document).on( 'wplink-open', function( wrap ) {
      if ( jQuery( 'input#wp-link-url' ).val() <= 0 )
        jQuery( 'input#wp-link-target' ).prop('checked', true );
    });
  </script>
  <?php
}
add_action( 'admin_footer-post-new.php', 'default_target_blank', 10, 0 );
add_action( 'admin_footer-post.php', 'default_target_blank', 10, 0 );


/**
 * Exclude posts of category video from search engines
 **/
function wpse_filter() {
  if ((is_singular() && in_category('video'))) {
    print '<meta name="robots" content="noindex">';
  }
}
add_action( 'wp_head', 'wpse_filter' );


/** 
 * Run content filter on all WYSIWIG values (fix for syntax highlighter plugin)
 **/
add_filter('acf/format_value/type=wysiwyg', 'format_value_wysiwyg', 10, 3);
function format_value_wysiwyg( $value, $post_id, $field ) {
  $value = apply_filters( 'the_content', $value );
  return $value;
}

