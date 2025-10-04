<?php
namespace NfiniteMediaPlayer;
if ( ! defined('ABSPATH') ) { exit; }
class CPT {
    public static function init(){
        add_action('init', [__CLASS__, 'register']);
        add_action('add_meta_boxes', [__CLASS__, 'metaboxes']);
        add_action('save_post', [__CLASS__, 'save_meta']);
        add_filter('manage_nmp_track_posts_columns', [__CLASS__, 'cols']);
        add_action('manage_nmp_track_posts_custom_column', [__CLASS__, 'col_content'], 10, 2);
    }
    public static function register(){
        register_post_type('nmp_track', [
            'label' => __('Tracks','nfinite-media-player'),
            'public' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-format-audio',
            'supports' => ['title','thumbnail','custom-fields'],
            'has_archive' => false,
            'show_in_rest' => true,
        ]);
    }
    public static function metaboxes(){
        add_meta_box('nmp_meta', __('Track Details','nfinite-media-player'), [__CLASS__,'box'], 'nmp_track', 'normal', 'high');
    }
    public static function box($post){
        wp_nonce_field('nmp_meta_save','nmp_meta_nonce');
        $audio = get_post_meta($post->ID, '_nmp_audio_url', true);
        $product = get_post_meta($post->ID, '_nmp_product_url', true);
        $bpm = get_post_meta($post->ID, '_nmp_bpm', true);
        $key = get_post_meta($post->ID, '_nmp_key', true);
        ?>
        <p><label><strong><?php _e('Audio Preview URL (mp3/wav)','nfinite-media-player'); ?></strong><br>
        <input type="url" style="width:100%" name="_nmp_audio_url" value="<?php echo esc_attr($audio); ?>" placeholder="https://.../preview.mp3"></label></p>
        <p><label><strong><?php _e('Product/Buy URL','nfinite-media-player'); ?></strong><br>
        <input type="url" style="width:100%" name="_nmp_product_url" value="<?php echo esc_attr($product); ?>" placeholder="https://yoursite.com/product/beat-name"></label></p>
        <p style="display:flex; gap:12px">
            <label style="flex:1"><strong><?php _e('BPM','nfinite-media-player'); ?></strong><br>
            <input type="number" min="0" name="_nmp_bpm" value="<?php echo esc_attr($bpm); ?>" placeholder="140"></label>
            <label style="flex:1"><strong><?php _e('Key','nfinite-media-player'); ?></strong><br>
            <input type="text" name="_nmp_key" value="<?php echo esc_attr($key); ?>" placeholder="F# minor"></label>
        </p>
        <?php
    }
    public static function save_meta($post_id){
        if ( ! isset($_POST['nmp_meta_nonce']) || ! wp_verify_nonce($_POST['nmp_meta_nonce'], 'nmp_meta_save') ) return;
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
        if ( ! current_user_can('edit_post', $post_id) ) return;
        foreach(['_nmp_audio_url','_nmp_product_url','_nmp_bpm','_nmp_key'] as $f){
            if(isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
        }
    }
    public static function cols($cols){ $cols['audio']=__('Audio','nfinite-media-player'); $cols['product']=__('Product URL','nfinite-media-player'); return $cols; }
    public static function col_content($col,$post_id){
        if($col==='audio'){ echo get_post_meta($post_id,'_nmp_audio_url',true) ? '<code>preview</code>' : '—'; }
        if($col==='product'){ $p=get_post_meta($post_id,'_nmp_product_url',true); echo $p ? '<a href="'.esc_url($p).'" target="_blank">link</a>' : '—'; }
    }
}
CPT::init();
