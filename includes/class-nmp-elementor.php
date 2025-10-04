<?php
namespace NfiniteMediaPlayer;
if ( ! defined('ABSPATH') ) { exit; }
if ( ! class_exists('\Elementor\Widget_Base') ) { return; }

class NMP_Elementor_Player extends \Elementor\Widget_Base {
    public function get_name(){ return 'nmp_player'; }
    public function get_title(){ return __('Nfinite Media Player','nfinite-media-player'); }
    public function get_icon(){ return 'eicon-play'; }
    public function get_categories(){ return ['general']; }
    public function get_style_depends(){ return ['nmp-player']; }
    public function get_script_depends(){ return ['nmp-player']; }

    protected function register_controls(){
        // =========================
        // CONTENT
        // =========================
        $this->start_controls_section('content', [ 'label' => __('Content','nfinite-media-player') ]);

        $this->add_control('source', [
            'label'   => __('Source','nfinite-media-player'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'tracks'   => __('Tracks (IDs)','nfinite-media-player'),
                'playlist' => __('Playlist','nfinite-media-player')
            ],
            'default' => 'tracks',
        ]);

        $this->add_control('ids', [
            'label'       => __('Track IDs (comma-separated)','nfinite-media-player'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => '12,34,56',
            'condition'   => ['source'=>'tracks']
        ]);

        $this->add_control('playlist_id', [
            'label'     => __('Playlist','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => self::get_playlists_options(),
            'default'   => '0',
            'condition' => ['source'=>'playlist']
        ]);

        $this->add_control('theme', [
            'label'   => __('Theme','nfinite-media-player'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => ['dark'=>'Dark','light'=>'Light'],
            'default' => 'dark',
        ]);

        $this->add_control('buy_label', [
            'label'   => __('Buy Button Label','nfinite-media-player'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Buy',
        ]);

        $this->add_control('show_meta', [
            'label'        => __('Show BPM/Key','nfinite-media-player'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        // =========================
        // STYLE — Player
        // =========================
        $this->start_controls_section('style_section', [
            'label' => __('Player Style','nfinite-media-player'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('bg_color', [
            'label'     => __('Background','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player' => '--nmp-bg: {{VALUE}};'],
        ]);

        $this->add_control('text_color', [
            'label'     => __('Text','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player' => '--nmp-text: {{VALUE}};'],
        ]);

        $this->add_control('ctrl_bg', [
            'label'     => __('Control Buttons BG','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player' => '--nmp-ctrl-bg: {{VALUE}};'],
        ]);

        $this->add_responsive_control('radius', [
            'label'     => __('Player Corner Radius','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
            'selectors' => ['{{WRAPPER}} .nmp-player' => '--nmp-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('button_radius', [
            'label'     => __('Button Corner Radius','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 24 ] ],
            'selectors' => ['{{WRAPPER}} .nmp-player' => '--nmp-button-radius: {{SIZE}}{{UNIT}};'],
        ]);

        // Spacing
        $this->add_responsive_control('player_padding', [
            'label'      => __('Player Padding','nfinite-media-player'),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors'  => ['{{WRAPPER}} .nmp-player' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('player_margin', [
            'label'      => __('Player Margin','nfinite-media-player'),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors'  => ['{{WRAPPER}} .nmp-player' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        // Buy Button (Option B — direct .nmp-buy controls)
        $this->add_control('buy_button_heading', [
            'type'      => \Elementor\Controls_Manager::HEADING,
            'label'     => __('Buy Button','nfinite-media-player'),
            'separator' => 'before',
        ]);

        $this->add_control('buy_text_color', [
            'label'     => __('Text Color','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-buy' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('buy_text_color_hover', [
            'label'     => __('Text Color (Hover)','nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-buy:hover' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('buy_bg_color', [
    'label'     => __('Background','nfinite-media-player'),
    'type'      => \Elementor\Controls_Manager::COLOR,
    'selectors' => [
        '{{WRAPPER}} .nmp-player .nmp-buy' => 'background: {{VALUE}}; background-image: none;',
    ],
]);

        $this->add_control('buy_bg_color_hover', [
    'label'     => __('Background (Hover)','nfinite-media-player'),
    'type'      => \Elementor\Controls_Manager::COLOR,
    'selectors' => [
        '{{WRAPPER}} .nmp-player .nmp-buy:hover' => 'background: {{VALUE}}; background-image: none;',
    ],
]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'buy_typography',
                'label'    => __('Typography','nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-buy',
            ]
        );

        $this->end_controls_section();

        // =========================
        // STYLE — Typography
        // =========================
        $this->start_controls_section('typography_section', [
            'label' => __('Typography', 'nfinite-media-player'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        // Base (entire player)
        $this->add_control('nmp_typo_base_heading', [
            'type'  => \Elementor\Controls_Manager::HEADING,
            'label' => __('Base', 'nfinite-media-player'),
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_base',
                'label'    => __('Base Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player',
            ]
        );

        // Title
        $this->add_control('nmp_typo_title_heading', [
            'type'      => \Elementor\Controls_Manager::HEADING,
            'label'     => __('Track Title', 'nfinite-media-player'),
            'separator' => 'before',
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_title',
                'label'    => __('Title Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-title',
            ]
        );
        $this->add_control('nmp_title_color', [
            'label'     => __('Title Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-title' => 'color: {{VALUE}};'],
        ]);

        // Artist
        $this->add_control('nmp_typo_artist_heading', [
            'type'      => \Elementor\Controls_Manager::HEADING,
            'label'     => __('Artist', 'nfinite-media-player'),
            'separator' => 'before',
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_artist',
                'label'    => __('Artist Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-artist',
            ]
        );
        $this->add_control('nmp_artist_color', [
            'label'     => __('Artist Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-artist' => 'color: {{VALUE}};'],
        ]);

        // Secondary Meta
        $this->add_control('nmp_typo_meta_heading', [
            'type'      => \Elementor\Controls_Manager::HEADING,
            'label'     => __('Secondary Meta', 'nfinite-media-player'),
            'separator' => 'before',
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_meta',
                'label'    => __('Meta Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-meta',
            ]
        );
        $this->add_control('nmp_meta_color', [
            'label'     => __('Meta Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-meta' => 'color: {{VALUE}};'],
        ]);

        // Playlist Rows
        $this->add_control('nmp_typo_playlist_heading', [
            'type'      => \Elementor\Controls_Manager::HEADING,
            'label'     => __('Playlist Rows', 'nfinite-media-player'),
            'separator' => 'before',
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_row',
                'label'    => __('Row Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-row',
            ]
        );
        $this->add_control('nmp_row_color', [
            'label'     => __('Row Text Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-row' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'nmp_typo_row_title',
                'label'    => __('Row Title Typography', 'nfinite-media-player'),
                'selector' => '{{WRAPPER}} .nmp-player .nmp-row-title',
            ]
        );
        $this->add_control('nmp_row_title_color', [
            'label'     => __('Row Title Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .nmp-player .nmp-row-title' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('nmp_row_active_color', [
            'label'     => __('Active Row Color', 'nfinite-media-player'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .nmp-player .nmp-row.is-active, {{WRAPPER}} .nmp-player .nmp-row.is-playing' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $theme     = $s['theme'] ?? 'dark';
        $buy_label = $s['buy_label'] ?? 'Buy';
        $show_meta = (($s['show_meta'] ?? '') === 'yes') ? 'yes' : 'no';

        if ( ($s['source'] ?? 'tracks') === 'playlist' ) {
            $pl_id = absint($s['playlist_id'] ?? 0);
            if ( $pl_id && class_exists('\NfiniteMediaPlayer\Playlist_CPT') ) {
                $track_ids = \NfiniteMediaPlayer\Playlist_CPT::get_playlist_tracks($pl_id);
                if ( ! empty($track_ids) ) {
                    echo \NfiniteMediaPlayer\Renderer::render_player([
                        'ids'       => implode(',', array_map('absint', $track_ids)),
                        'theme'     => $theme,
                        'buy_label' => $buy_label,
                        'show_meta' => $show_meta,
                    ]);
                    return;
                }
            }
            echo '<div class="nmp-empty">'.esc_html__('This playlist has no tracks or the playlist is missing.','nfinite-media-player').'</div>';
            return;
        }

        echo \NfiniteMediaPlayer\Renderer::render_player([
            'ids'       => $s['ids'] ?? '',
            'theme'     => $theme,
            'buy_label' => $buy_label,
            'show_meta' => $show_meta,
        ]);
    }

    private static function get_playlists_options(){
        $opts = [ '0' => __('— Select a playlist —','nfinite-media-player') ];
        if ( post_type_exists('nmp_playlist') ) {
            $q = new \WP_Query([
                'post_type'      => 'nmp_playlist',
                'posts_per_page' => 200,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ]);
            if ( $q->have_posts() ) {
                foreach( $q->posts as $pid ){
                    $opts[(string)$pid] = get_the_title($pid) ?: ('#'.$pid);
                }
            }
        }
        return $opts;
    }
}
