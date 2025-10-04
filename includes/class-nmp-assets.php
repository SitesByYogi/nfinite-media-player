<?php
namespace NfiniteMediaPlayer;
if ( ! defined('ABSPATH') ) { exit; }

class Assets {
    public static function init(){
        add_action('init', [__CLASS__, 'register']);
        add_action('elementor/editor/after_enqueue_styles', [__CLASS__, 'register']);
        add_action('elementor/editor/after_enqueue_scripts', [__CLASS__, 'register']);
    }
    public static function register(){
        if ( ! wp_style_is('nmp-player', 'registered') ) {
            wp_register_style('nmp-player', NMP_URL.'public/css/player.css', [], NMP_VER);
        }
        if ( ! wp_script_is('nmp-player', 'registered') ) {
            wp_register_script('nmp-player', NMP_URL.'public/js/player.js', [], NMP_VER, true);
        }
    }
}
Assets::init();
