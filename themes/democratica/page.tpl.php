<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language ?>" xml:lang="<?php print $language ?>">
<head>
  <title>
  <?php
    if ($title == ""):
      print $site_name .' - '. t($site_slogan);
    else:
      print $title .' - '. $site_name;
    endif;
  ?></title>
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="<?php global $base_url; print $base_url; ?>/" />
  
  <!-- styles -->
  <?php print $styles ?>
  <!-- end styles -->

  <link rel="stylesheet" href="themes/democratica/basics.css" type="text/css" media="screen" />
  <link rel="alternate stylesheet" href="themes/democratica/print/styles.css"  type="text/css" media="print" title="Print Formatting" />
  <style type="text/css" media="screen">
  /*<![CDATA[*/
  <!--
    @import url(themes/democratica/layout.css);
    @import url(themes/democratica/styles.css);
    @import url(themes/democratica/modules.css);
  // -->
  /*]]>*/
  </style>
  <link rel="icon" href="themes/democratica/favicon.ico" type="image/ico" />
  <link rel="shortcut icon" href="themes/democratica/favicon.ico" />
</head>
<?php
$classes = array();
$classes[] = 'layout-'. $layout;
if ($_GET['q']) {
  $classes[] = preg_replace('/\d+-/', '', str_replace('/', '-', $_GET['q']));
}
if (arg(1)) {
  $classes[] = arg(0) . '-' . arg(1);
}
$classes[] = 'page-'. str_replace('/', '-', $_GET['q']);
?>
<body <?php print theme("onload_attribute"); ?> class="<?php print implode(' ', $classes) ?>">

<!-- Accessibility & Search engine optimization -->
<div class="hide">
  <?php if ($site_name) : ?>
    <h1><a href="<?php print url(); ?>/" title="Index Page"><?php print($site_name)?></a> <?php if ($mission != ""): ?> &mdash; <?php print($mission) ?><?php endif; ?></h1>
  <?php endif;?>
  <p>
    <?php print l(t('Skip to content'), $_GET['q'],array('title' =>'Skip directly to the content'),NULL,'main-content',FALSE) ?>
  </p>
</div>

<div id="outer-wrapper">

<!-- START: BRANDING                 |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

<div id="branding">
  <?php if ($logo) : ?>
    <div id="logo"><a href="<?php print url() ?>" title="Index Page"><img src="<?php print($logo) ?>" alt="<?php print($site_name) ?> Logo" /></a></div>
  <?php else: ?>
    <?php if ($site_name) : ?>
      <h1 id="site-name"><a href="<?php print url() ?>" title="Index Page"><?php print($site_name) ?></a></h1>
    <?php endif;?>
  <?php endif;?>
  <?php if ($site_slogan) : ?>
    <div id="site-slogan"><?php print($site_slogan) ?></div>
  <?php endif;?>
</div>

<hr class="hide" />

<!-- END: BRANDING                   |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  


  <div id="wrapper">
    <div id="container">
      <div id="content">

<!-- START: LEFT REGION              |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

        <?php if ($sidebar_left != '') { ?>
        <div class="sidebar" id="sidebar-left">
          
          <?php if ( $layout == "left" || "both" ): ?>          
            <!-- START: NAVIGATION               |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

            <div id="navigation">
              <h2 class="hide">Site Navigation</h2>
            <?php if (is_array($primary_links)) : ?>
              <ul id="nav-primary">
              <?php foreach ($primary_links as $link): ?>
                <li><?php print $link?></li>
              <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <?php if (is_array($secondary_links) && (count($secondary_links) != 0)) : ?>
              <ul id="nav-secondary">
              <?php foreach ($secondary_links as $link): ?>
                <li><?php print $link?></li>
              <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            </div>

            <hr class="hide" />

            <!-- END: NAVIGATION                 |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  
          <?php endif; ?>

          <?php if ($search_box && $layout == "left" ): ?>
            <!-- START: SEARCH                   |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  
            <div class="block block-search" id="block-search">
              <h2>Search this site</h2>
              <div class="content">
                <form action="<?php print $search_url ?>" method="post" id="search">
                  <input class="form-text" type="text" size="15" value="" name="keys" /><input class="form-submit" type="submit" value="<?php print $search_button_text ?>" />
                </form>          
              </div>
            </div>

            <hr class="hide" />

            <!-- END: SEARCH                     |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  
          <?php endif; ?>

          <?php print(word_split($sidebar_left, 15)); ?>
        </div>
        <?php } ?>

<!-- END: LEFT REGION                |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  


<!-- START: CONTENT                  |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

        <div id="main-content">
            <?php if (($_GET['q']) != variable_get('site_frontpage','node')): /* this prevents breadcrumb from showing up on homepage */ ?>    
              <?php if ($breadcrumb != ""): ?>
                <div id="breadcrumbs">
                  <?php print $breadcrumb;?>
                </div>
                <hr class="hide" />
              <?php endif; ?>
            <?php endif; ?>
    
            <?php if ($mission != ""): ?>
              <div id="mission">
                <?php print $mission ?>
              </div>
            <?php endif; ?>
    
            <?php if ($title != ""): ?>
              <h1 class="page-title"><?php print $title ?></h1>
            <?php endif; ?>

            <?php if ($tabs != ""): ?>
              <?php print $tabs ?>
            <?php endif; ?>
            
            <?php if ($messages != ""): ?>
              <div class="message"><?php print $messages ?></div>
            <?php endif; ?>
    
            <?php if ($help != ""): ?>
              <div id="help"><?php print $help ?></div>
            <?php endif; ?>
            
            <div id="body-content">
              <?php print($content) ?>
            </div>
        </div>

        <hr class="hide" />

<!-- END: CONTENT                    |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  

      </div><!-- end #content -->
    </div><!-- end #container -->

<!-- START: SECONDARY CONTENT        |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

<?php if ($sidebar_right != '') : ?>

    <div class="sidebar" id="sidebar-right">

      <?php if ( $layout == "right" ): ?>
        <!-- START: NAVIGATION               |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

        <div id="navigation">
          <h2 class="hide">Site Navigation</h2>
        <?php if (is_array($primary_links)) : ?>
          <ul id="nav-primary">
          <?php foreach ($primary_links as $link): ?>
            <li><?php print $link?></li>
          <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if (is_array($secondary_links) && (count($secondary_links) != 0)) : ?>
          <ul id="nav-secondary">
          <?php foreach ($secondary_links as $link): ?>
            <li><?php print $link?></li>
          <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        </div>

        <hr class="hide" />

        <!-- END: NAVIGATION                 |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  
      <?php endif; ?>

      <?php if ($search_box && $layout == "right" || "both" ): ?>

      <!-- START: SEARCH                   |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  
      <div class="block block-search" id="block-search">
        <h2>Search this site</h2>
        <div class="content">
          <form action="<?php print $search_url ?>" method="post" id="search">
            <input class="form-text" type="text" size="15" value="" name="keys" /><input class="form-submit" type="submit" value="<?php print $search_button_text ?>" />
          </form>          
        </div>
      </div>

      <hr class="hide" />

      <!-- END: SEARCH                     |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  

      <?php endif; ?>

      <?php print(word_split($sidebar_right, 30)); ?>
    </div>

<?php endif;?>

    <div class="clearing"></div>
  </div><!-- end #wrapper -->

  <hr class="hide" />

<!-- END: SECONDARY CONTENT          |||||||||||||||||||||||||||||||||||||||||||||||||||||||| --> 


<!-- START: NOTICES                  |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->

<div id="notices">
<?php if ($footer_message) : ?>
    <?php print $footer_message;?>
<?php endif; ?>
<?php print $closure;?>
</div>

<!-- END: NOTICES                    |||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->  

</div><!-- end #outer-wrapper -->

</body>
</html>
