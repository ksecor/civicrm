<div class="node<?php print ($sticky) ? " sticky" : ""; ?>"> 
  <?php if ($page == 0): ?> 
  <h2 class="page-title"><a href="<?php print $node_url ?>" rel="bookmark" title="Permanent Link to <?php print $title ?>"><?php print $title ?></a></h2> 
  <?php endif; ?> 
  <small><?php print $submitted ?></small> 
  <div class="content"> 
    <?php print $content ?> 
  </div> 
  <?php if ($links): ?>
  <div class="links">
  <?php if ($terms): ?> <span class="postmetadata">Posted in <?php print $terms ?></span> | <?php endif; ?><?php print $links ?> &#187;</p> 
  </div>
  <?php endif; ?> 
</div>
