<?php
    global $nmjson;
    if(!isset($nmjson)){
        $nmjson = new nmjson();
    }

    add_shortcode('nm', 'nm_shortcode');
    function nm_shortcode( $atts, $content = null ) {
        return get_netease_music();
    }
	
	add_shortcode('nms', 'nm_single_song_shortcode');
	function nm_single_song_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(

            'auto' => "",

        ),

        $atts ) );
	$class = ($auto && !wp_is_mobile()) ? " auto" : "";	
	
    return '<div class="nmsingle-audio fontSmooth'.$class.'" songid="'
    . do_shortcode( $content ) . '" data-type="song"><div class="xiami-cover"><div class="play-button"><i class="fxfont"></i></div></div><div class="play-prosess"><div class="play-prosess-bar"></div></div><div class="play-timer">--:--</div><div class="audio-player"><div class="audio-name"></div><div class="audio-author"></div></div><div class="audio-jplayer"></div></div>';
}

    function netease_music(){

        echo get_netease_music();
    }

    function get_netease_music(){
        global $nmjson;
        $index = 0;
        $userid = nm_get_setting('id') ? nm_get_setting('id') : 30829298;
        $row = nm_get_setting('number') ? nm_get_setting('number') : 4;
        $contents = $nmjson->netease_user($userid);
		array_shift($contents);
        $per_page = nm_get_setting('perpage') ? nm_get_setting('perpage') : 16;
        $count  = count($contents);
        $max_page = ceil($count/$per_page);
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $contents = array_slice( $contents,( ( $paged-1 )* $per_page ), $per_page );
		$is_small =  nm_get_setting('small');
		$css = 'album--nice-list';
		$size = nm_get_setting('coverwidth') ? nm_get_setting('coverwidth') : 180;
		
		$style = '<style>
		.album--nice{width:'.$size.'px}
		.album--nice .fxfont{height:'.$size.'px;line-height:'.$size.'px;}
		</style>';
        $output = $style.'<div class="'. $css .'">';
        foreach($contents as $content){
            $index ++;
            $output .= '<div class="album--nice" data-type="163collect" data-thumbnail="'.$content['playlist_coverImgUrl'].'" data-id="'.$content['playlist_id'].'"  data-tooltip="'.$content['playlist_name'].'"><img src="'.$content['playlist_coverImgUrl'].'?param='.$size.'y'.$size.'">
<i class="fxfont"></i><span class="music-info">'.$content['playlist_name'].'</span>
</div>';

            if( $index%$row==0 && $index < $per_page) $output .= '</div><div class="'. $css .'">';
        }
        $output .='</div><div class="nm-copyright"><i class="fxfont icon-note"></i> <a href="http://fatesinger.com/74369" target="_blank" title="网易云音乐">网易云音乐</a></div><div class="music-page-navi">';
        $format = '/page/%#%';
        $big = 999999999; // need an unlikely integer
        $output .= paginate_links( array(
            'base' => get_pagelink()."%_%",
            'format' => $format,
            'current' => max( 1, $paged ),
            'total' => $max_page,
            'prev_next'    => FALSE
        ) );
        $output .='</div><div class="nmplaybar"><div class="nmplayer-prosess"></div><div class="nmplayer-wrap"><div class="nmplayer-info"><span class="nmplayer-title"></span><span class="nmplayer-time"></span><span class="nmplayer-lrc"></span></div><div class="nmplayer-control">
<ul>
<li>
<a id="nmplayer-prev" href="javascript:;">
<span class="icon-previous fxfont "></span>
</a>
</li>
<li>
<a id="nmplayer-button" href="javascript:;">
<span class="icon-pause fxfont "></span>
</a>
</li>
<li>
<a id="nmplayer-next" href="javascript:;">
<span class="icon-next fxfont"></span>
</a>
</li>
</ul>
</div></div></div>';
        return $output;

    }

    add_action('admin_menu', 'nm_menu');

    function nm_menu() {
        add_options_page('网易云音乐设置', '网易云音乐设置', 'manage_options', basename(__FILE__), 'nm_setting_page');
        add_action( 'admin_init', 'nm_setting_group');
    }

    function nm_scripts(){
        wp_enqueue_style( 'nm', nm_css_url('style'), array(), NM_VERSION );
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'nm',  nm_js_url('base'), array(), NM_VERSION,true );
        wp_localize_script( 'nm', 'nm_ajax_url', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'swfurl' => nm_css_url('Jplayer.swf'),
            'nonce' =>wp_create_nonce('bigfa'),
			'tworow' =>nm_get_setting('tworow')
        ));
    }
    add_action('wp_enqueue_scripts', 'nm_scripts', 20, 1);

    add_action( 'wp_ajax_nopriv_nmjson', 'nmjson_callback' );
    add_action( 'wp_ajax_nmjson', 'nmjson_callback' );
    function nmjson_callback() {
        global $nmjson;

        $id = $_GET['id'];
		$type = $_GET['type'];
		if($type == 'lrc'){
		$song = array();
		$song = $nmjson->get_song_lrc($id);
		}elseif ($type == 'song') {
		 $song = $nmjson->netease_song($id);
		}
		
		
		else{
        $song = $nmjson->netease_playlist($id);
		}
        $result = array(
            'msg' => 200,
            'song' => $song
        );

        header('Content-type: application/json');
        echo json_encode($result);
        exit;
    }
	
    function get_pagelink(){
        $slug = nm_get_setting('pagename');
        if($slug){
            $slug = get_permalink( get_page_by_path($slug) );
            $slug = rtrim($slug,'/\\');
            return $slug;
        }
        return false;
    }

    function nm_setting_group() {
        register_setting( 'nm_setting_group', 'nm_setting' );
    }

    function nm_setting_page(){
        @include 'nm-setting.php';
    }

    function nm_get_setting($key=NULL){
        $setting = get_option('nm_setting');
        return $key ? $setting[$key] : $setting;
    }

    function nm_delete_setting(){
        delete_option('nm_setting');
    }

    function nm_setting_key($key){
        if( $key ){
            return "nm_setting[$key]";
        }

        return false;
    }
    function nm_update_setting($setting){
        update_option('nm_setting', $setting);
    }

    function nm_css_url($css_url){
        return NM_URL . "/static/css/{$css_url}.css";
    }

    function nm_js_url($js_url){
        return NM_URL . "/static/js/{$js_url}.js";
    }