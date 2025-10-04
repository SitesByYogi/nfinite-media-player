<?php /** @var array $tracks */ ?>
<div class="nmp-player nmp-theme-<?php echo esc_attr($theme); ?>" data-buy-label="<?php echo esc_attr($buy_label); ?>">
  <div class="nmp-now">
    <div class="nmp-cover"><img alt="" /></div>
    <div class="nmp-meta">
      <div class="nmp-title"></div>
      <?php if ($show_meta): ?>
      <div class="nmp-small"><span class="nmp-bpm"></span> <span class="nmp-sep">•</span> <span class="nmp-key"></span></div>
      <?php endif; ?>
      <div class="nmp-controls">
        <button class="nmp-prev" aria-label="Previous">⏮</button>
        <button class="nmp-toggle" aria-label="Play/Pause">▶</button>
        <button class="nmp-next" aria-label="Next">⏭</button>
        <div class="nmp-time"><span class="nmp-current">0:00</span> / <span class="nmp-duration">0:00</span></div>
      </div>
      <div class="nmp-progress"><div class="nmp-bar"><span class="nmp-fill"></span></div></div>
      <div class="nmp-actions">
  <a class="nmp-buy nmp-cta"
     target="_blank"
     rel="nofollow noopener"
     data-buy-label="<?php echo esc_attr($buy_label); ?>">
     <?php echo esc_html($buy_label); ?>
  </a>
</div>
    </div>
  </div>
  <ol class="nmp-list">
    <?php foreach($tracks as $t): ?>
      <li class="nmp-item"
          data-audio="<?php echo esc_attr($t['audio']); ?>"
          data-title="<?php echo esc_attr($t['title']); ?>"
          data-cover="<?php echo esc_attr($t['cover']); ?>"
          data-product="<?php echo esc_attr($t['product']); ?>"
          data-bpm="<?php echo esc_attr($t['bpm']); ?>"
          data-key="<?php echo esc_attr($t['key']); ?>">
        <span class="nmp-item-title"><?php echo esc_html($t['title']); ?></span>
        <?php if(!empty($t['bpm']) || !empty($t['key'])): ?>
          <span class="nmp-item-meta"><?php echo esc_html(trim($t['bpm'].' BPM '.($t['key']?'• '.$t['key']:''))); ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</div>
