<?php
namespace NfiniteMediaPlayer;
if ( ! defined('ABSPATH') ) { exit; }
class Renderer {
    public static function enqueue_front(){
        if ( ! wp_style_is('nmp-player', 'enqueued') ) wp_enqueue_style('nmp-player');
        if ( ! wp_script_is('nmp-player', 'enqueued') ) wp_enqueue_script('nmp-player');
    }
    public static function get_tracks_by_ids($ids_csv){
        $ids = array_filter(array_map('absint', explode(',', $ids_csv)));
        if (empty($ids)) return [];
        $q = new \WP_Query([
            'post_type' => 'nmp_track',
            'post__in'  => $ids,
            'orderby'   => 'post__in',
            'posts_per_page' => count($ids),
            'no_found_rows' => true,
        ]);
        $tracks = [];
        while($q->have_posts()){ $q->the_post();
            $id = get_the_ID();
            $tracks[] = [
                'id'=>$id,'title'=>get_the_title(),
                'cover'=> get_the_post_thumbnail_url($id,'medium') ?: '',
                'audio'=> (string) get_post_meta($id,'_nmp_audio_url',true),
                'product'=> (string) get_post_meta($id,'_nmp_product_url',true),
                'bpm'=> (string) get_post_meta($id,'_nmp_bpm',true),
                'key'=> (string) get_post_meta($id,'_nmp_key',true),
            ];
        }
        wp_reset_postdata();
        return $tracks;
    }
    public static function render_player($atts){
    self::enqueue_front();
    $tracks = self::get_tracks_by_ids($atts['ids']);
    if (empty($tracks)) return '<div class="nmp-empty">'.esc_html__('No tracks found.','nfinite-media-player').'</div>';
    ob_start();

    // ✅ Normalize values passed to the template
    $theme      = $atts['theme'] ?? 'dark';
    $buy_label  = (isset($atts['buy_label']) && $atts['buy_label'] !== '')
        ? $atts['buy_label']
        : __('Buy', 'nfinite-media-player');
    $show_meta  = (isset($atts['show_meta']) && $atts['show_meta'] === 'yes');

    include NMP_PATH . 'templates/player.php';
    return ob_get_clean();
}

}
