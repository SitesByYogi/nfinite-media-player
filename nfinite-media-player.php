<?php
/*
Plugin Name: Nfinite Media Player
Description: Sleek WordPress audio player for hip-hop producers & artists with Elementor widget, Playlists, and Buy links.
Version: 0.1.4
Author: SitesByYogi
License: GPLv2 or later
Text Domain: nfinite-media-player
*/

if ( ! defined('ABSPATH') ) { exit; }

// Constants
define('NMP_VER', '0.1.4');
define('NMP_PATH', plugin_dir_path(__FILE__));
define('NMP_URL', plugin_dir_url(__FILE__));

// Includes
require_once NMP_PATH . 'includes/class-nmp-assets.php';
require_once NMP_PATH . 'includes/class-nmp-cpt.php';
require_once NMP_PATH . 'includes/class-nmp-render.php';
require_once NMP_PATH . 'includes/class-nmp-playlist.php';

// Elementor: load widget only when Elementor registers widgets
add_action('elementor/widgets/register', function( $widgets_manager ){
    require_once NMP_PATH . 'includes/class-nmp-elementor.php';
    if ( class_exists('\NfiniteMediaPlayer\NMP_Elementor_Player') ) {
        $widgets_manager->register( new \NfiniteMediaPlayer\NMP_Elementor_Player() );
    }
});

// Shortcode: [nfinite_media_player ids="1,2,3" theme="dark" buy_label="Purchase" show_meta="yes"]
add_shortcode('nfinite_media_player', function($atts){
    $atts = shortcode_atts([
        'ids' => '',
        'theme' => 'dark',
        'buy_label' => 'Buy',
        'show_meta' => 'yes',
    ], $atts, 'nfinite_media_player');

    return \NfiniteMediaPlayer\Renderer::render_player($atts);
});

// Shortcode: [nmp_playlist id="123" theme="dark" buy_label="Buy" show_meta="yes"]
add_shortcode('nmp_playlist', function($atts){
    $atts = shortcode_atts([
        'id' => 0,
        'theme' => 'dark',
        'buy_label' => 'Buy',
        'show_meta' => 'yes',
    ], $atts, 'nmp_playlist');

    $pl_id = absint($atts['id']);
    if (!$pl_id) return '';
    $track_ids = \NfiniteMediaPlayer\Playlist_CPT::get_playlist_tracks($pl_id);
    if (empty($track_ids)) {
        return '<div class="nmp-empty">'.esc_html__('This playlist has no tracks yet.','nfinite-media-player').'</div>';
    }
    return \NfiniteMediaPlayer\Renderer::render_player([
        'ids' => implode(',', array_map('absint', $track_ids)),
        'theme' => $atts['theme'],
        'buy_label' => $atts['buy_label'],
        'show_meta' => $atts['show_meta'],
    ]);
});
