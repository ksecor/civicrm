<div class="comment <?php print ($comment->new) ? 'comment-new' : '' ?>">
<?php if ($comment->new) : ?>
  <a id="new"></a>
  <span class="new"><?php print $new ?></span>
<?php endif; ?>

<div class="title"><?php print $title ?></div>
  <div class="content">
    <?php if ($picture) : ?>
    <div class="user-picture">
      <?php print $picture ?>
    </div>
    <?php endif; ?>
    <?php print $content ?>
  </div>
  <?php if ($picture) : ?>
    <br class="clear" />
  <?php endif; ?>
  <div class="author">
    <?php print $submitted; ?>
  </div>
  <div class="links">
    <?php print $links ?>
  </div>
</div>
