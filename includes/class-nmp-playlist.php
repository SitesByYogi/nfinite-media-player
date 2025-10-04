<?php
namespace NfiniteMediaPlayer;
if ( ! defined('ABSPATH') ) { exit; }

class Playlist_CPT {
    const CPT = 'nmp_playlist';
    const META_TRACKS = '_nmp_playlist_tracks';

    public static function init(){
        add_action('init', [__CLASS__, 'register']);
        add_action('add_meta_boxes', [__CLASS__, 'metabox']);
        add_action('save_post', [__CLASS__, 'save']);
        add_filter('manage_' . self::CPT . '_posts_columns', [__CLASS__, 'cols']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [__CLASS__, 'col_content'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_assets']);
    }

    public static function register(){
        register_post_type(self::CPT, [
            'label' => __('Playlists','nfinite-media-player'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-playlist-audio',
            'supports' => ['title'],
            'has_archive' => false,
            'show_in_rest' => false,
        ]);
    }

    public static function admin_assets($hook){
        global $post_type;
        if ( $post_type !== self::CPT ) return;
        wp_enqueue_script('jquery-ui-sortable');
        $js = <<<JS
        jQuery(function($){
            var $list = $('#nmp-pl-tracks');
            $list.sortable({ handle: '.nmp-handle', placeholder: 'nmp-sort-placeholder' });
            $('#nmp-pl-filter').on('input', function(){
                var term = $(this).val().toLowerCase();
                $list.children('li').each(function(){
                    var t = $(this).data('title') || '';
                    $(this).toggle( t.toLowerCase().indexOf(term) !== -1 );
                });
            });
            $('#nmp-pl-add').on('click', function(e){
                e.preventDefault();
                var id = parseInt($('#nmp-pl-picker').val(),10);
                if(!id) return;
                if( $('#nmp-pl-tracks li[data-id="'+id+'"]').length ) return;
                var title = $('#nmp-pl-picker option:selected').text();
                var li = $('<li class="nmp-pl-item" data-id="'+id+'" data-title="'+$('<div>').text(title).html()+'">                        <span class="nmp-handle">☰</span>                        <span class="nmp-title"></span>                        <a href="#" class="nmp-remove">Remove</a>                        <input type="hidden" name="nmp_playlist_tracks[]" value="'+id+'" />                    </li>');
                li.find('.nmp-title').text(title);
                $list.append(li);
            });
            $list.on('click', '.nmp-remove', function(e){ e.preventDefault(); $(this).closest('li').remove(); });
        });
        JS;
        $css = <<<CSS
        #nmp-pl-toolbar{display:flex;gap:8px;align-items:center;margin-bottom:10px}
        #nmp-pl-tracks{list-style:none;margin:0;padding:0}
        #nmp-pl-tracks li{display:flex;gap:10px;align-items:center;border:1px solid #ddd;background:#fff;padding:6px 8px;margin-bottom:6px}
        #nmp-pl-tracks .nmp-handle{cursor:move;color:#555}
        #nmp-pl-tracks .nmp-title{flex:1;font-weight:600}
        #nmp-pl-tracks .nmp-remove{color:#b32d2e;text-decoration:none}
        .nmp-sort-placeholder{height:36px;border:1px dashed #999;background:#f8f8f8;margin-bottom:6px}
        CSS;
        wp_add_inline_script('jquery-ui-sortable', $js);
        wp_add_inline_style('wp-components', $css);
    }

    public static function metabox(){
        add_meta_box('nmp_playlist_trax', __('Playlist Tracks','nfinite-media-player'), [__CLASS__,'box'], self::CPT, 'normal', 'high');
    }

    public static function box($post){
        $saved = get_post_meta($post->ID, self::META_TRACKS, true);
        $saved = is_array($saved) ? array_map('absint',$saved) : [];
        $tracks_q = new \WP_Query([
            'post_type' => 'nmp_track',
            'posts_per_page' => 200,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);
        $all_tracks = [];
        if ( $tracks_q->have_posts() ){
            foreach( $tracks_q->posts as $tid ){
                $all_tracks[$tid] = get_the_title($tid);
            }
        }
        echo '<div id="nmp-pl-toolbar">';
        echo '<select id="nmp-pl-picker">';
        echo '<option value="0">'.esc_html__('Select a track…','nfinite-media-player').'</option>';
        foreach($all_tracks as $id => $ttl){
            printf('<option value="%d">%s</option>', $id, esc_html($ttl));
        }
        echo '</select> ';
        echo '<button class="button" id="nmp-pl-add">'.esc_html__('Add Track','nfinite-media-player').'</button> ';
        echo '<input type="search" id="nmp-pl-filter" class="regular-text" placeholder="'.esc_attr__('Filter tracks…','nfinite-media-player').'" />';
        echo '</div>';

        echo '<ul id="nmp-pl-tracks">';
        foreach($saved as $id){
            if ( empty($all_tracks[$id]) ) continue;
            $title = $all_tracks[$id];
            echo '<li class="nmp-pl-item" data-id="'.esc_attr($id).'" data-title="'.esc_attr($title).'">';
            echo '<span class="nmp-handle">☰</span>';
            echo '<span class="nmp-title">'.esc_html($title).'</span>';
            echo '<a class="nmp-remove" href="#">'.esc_html__('Remove','nfinite-media-player').'</a>';
            echo '<input type="hidden" name="nmp_playlist_tracks[]" value="'.esc_attr($id).'" />';
            echo '</li>';
        }
        echo '</ul>';

        wp_nonce_field('nmp_pl_save', 'nmp_pl_nonce');
        echo '<p class="description">'.esc_html__('Drag to reorder. Use the picker to add tracks.','nfinite-media-player').'</p>';
        echo '<p><code>[nmp_playlist id="'.esc_attr($post->ID).'"]</code></p>';
    }

    public static function save($post_id){
        if ( get_post_type($post_id) !== self::CPT ) return;
        if ( ! isset($_POST['nmp_pl_nonce']) || ! wp_verify_nonce($_POST['nmp_pl_nonce'], 'nmp_pl_save') ) return;
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
        if ( ! current_user_can('edit_post', $post_id) ) return;
        $ids = isset($_POST['nmp_playlist_tracks']) ? (array) $_POST['nmp_playlist_tracks'] : [];
        $ids = array_values(array_unique(array_filter(array_map('absint',$ids))));
        update_post_meta($post_id, self::META_TRACKS, $ids);
    }

    public static function cols($cols){
        $cols['tracks'] = __('Tracks','nfinite-media-player');
        $cols['shortcode'] = __('Shortcode','nfinite-media-player');
        return $cols;
    }
    public static function col_content($col, $post_id){
        if ($col === 'tracks'){
            $ids = get_post_meta($post_id, self::META_TRACKS, true);
            echo esc_html( is_array($ids) ? count($ids) : 0 );
        }
        if ($col === 'shortcode'){
            echo '<code>[nmp_playlist id="'.esc_attr($post_id).'"]</code>';
        }
    }
    public static function get_playlist_tracks($playlist_id){
        $ids = get_post_meta($playlist_id, self::META_TRACKS, true);
        return is_array($ids) ? array_values(array_filter(array_map('absint',$ids))) : [];
    }
}
Playlist_CPT::init();
