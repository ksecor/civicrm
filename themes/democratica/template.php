<?php

/*******************************************************************************

Author:		Christopher Messina
Created:	2004-11-02
Updated: 2005-02-15
Copyright:	(cc) Share alike 2005, some rights reserved.

  Thanks to cxj and moshe in #drupal for their help.

*******************************************************************************/

function phptemplate_breadcrumb($breadcrumb) {
  $breadcrumb[] = drupal_get_title();
  return "<div class=\"hide\">You are here:</div>\n<ul>\n<li class=\"first\">". implode("</li>\n<li>", $breadcrumb) ."</li>\n</ul>\n";
}

function phptemplate_xml_icon($url) {
  if ($image = theme('image', 'themes/democratica/images/rss.png', t('XML feed'), t('XML feed'))) {
    return '<div class="xml-icon"><a href="'. $url .'" title="Subscribe to RSS feed">'. $image .'</a></div>';
  }
} 

function _word_split($text) {
  global $democratica_word_length;
  return preg_replace('/([^ ]{'. $democratica_word_length .'})(?=[^ ])/u', '\1-<wbr />', $text[0]);
}

function word_split($text, $max_char = 15) {
  global $democratica_word_length;
  $democratica_word_length = $max_char;
  return substr(preg_replace_callback('/>[^<]+</', '_word_split', '>'. $text .'<'), 1, -1);
}

function phptemplate_comment_thread_min($comment, $threshold, $pid = 0) {
  if (comment_visible($comment, $threshold)) {
    $output  = "<dl>\n";
    $output .= theme('comment_view', $comment, '', 0);
    $output .= "</dl>\n";
  }
  return $output;
}

function phptemplate_comment_thread_max($comment, $threshold, $level = 0) {
  $output = '';
  if ($comment->depth) {
    $output .= "<dl>\n";
  }

  $output .= theme('comment_view', $comment, theme('links', module_invoke_all('link', 'comment', $comment, 0)), comment_visible($comment, $threshold));

  if ($comment->depth) {
    $output .= "</dl>\n";
  }
  return $output;
}


function phptemplate_status_messages() {
  if ($data = drupal_get_messages()) {
    $output = '';
    foreach ($data as $type => $messages) {
      $output .= "<div class=\"messages $type\">\n";
        $output .= " <ul>\n";
        foreach($messages as $message) {
          $output .= '  <li>'. $message ."</li>\n";
        }
        $output .= " </ul>\n";
      $output .= "</div>\n";
    }
    return $output;
  }
} 

function phptemplate_aggregator_page_item($item) {
  static $last;

  $date = date('Ymd', $item->timestamp);
  if ($date != $last) {
    $last = $date;
    $output .= '<h2 class="aggregator-date">'. date('F j, Y', $item->timestamp) ."</h2>\n";
  }

  $output .= "<div class=\"news-item\">\n";
  $output .= " <h3 class=\"news-item-title\"><a href=\"$item->link\">$item->title</a></h3>\n";
  if ($item->ftitle && $item->fid) { $output .= '  <div class="news-item-source"> Source: '. l($item->ftitle, "aggregator/sources/$item->fid") .' @ <span class="news-item-date">'. date('H:i', $item->timestamp) ."</span>\n</div>\n"; }
  $output .= " <div class=\"news-item-body\">\n";
  if ($item->description) {
    $output .= "  <div class=\"news-item-description\">$item->description</div>\n";
  }

  $result = db_query('SELECT c.title, c.cid FROM {aggregator_category_item} ci LEFT JOIN {aggregator_category} c ON ci.cid = c.cid WHERE ci.iid = %d ORDER BY c.title', $item->iid);
  $categories = array();
  while ($category = db_fetch_object($result)) {
    $categories[] = l($category->title, 'aggregator/categories/'. $category->cid);
  }
  if ($categories) {
    $output .= '  <div class="news-item-categories">'. t('Categories') .': '. implode(', ', $categories) ."</div>\n";
  }

  $output .= " </div>\n";
  $output .= "</div>\n";

  return $output;
} 

/*
function phptemplate_menu_item($mid) {
  $menu = menu_get_menu();

  $link_mid = $mid;
  while ($menu['items'][$link_mid]['type'] & MENU_LINKS_TO_PARENT) {
    $link_mid = $menu['items'][$link_mid]['pid'];
  }

  $class = array();
  $local_tasks = menu_get_local_tasks();
  if (menu_in_active_trail($mid)) {
    $class = array('class' => 'current');
  }

  return l($menu['items'][$mid]['title'],
           $menu['items'][$link_mid]['path'],
           array_key_exists('description', $menu['items'][$mid]) ? array_merge($class, array("title" => $menu['items'][$mid]['description'])) : $class);
} 

function phptemplate_menu_local_task($mid, $active) {
  $local_tasks = menu_get_local_tasks();
  $pid = menu_get_active_nontask_item();
  $menu = menu_get_menu();
  if ($active) {
    if ($menu['items'][$mid]['children'][0] == menu_get_active_item()) {
      $output = '<li class="active"><span>'. $menu['items'][$mid]['title'] .'</span>';      
    }
    else {
      $output = '<li class="active">'. theme('menu_item', $mid) ."</li>\n"; 
    }
    foreach ($local_tasks[$pid]['children'] as $mid) {
      if (menu_in_active_trail($mid) && count($local_tasks[$mid]['children']) > 1) {
        $output .= "<ul class=\"secondary\">\n";
        foreach ($local_tasks[$mid]['children'] as $cid) {
          $output .= theme('menu_local_task', $cid, 0);
        }
        $output .= "</ul>\n";
      }
    }
    $output .= "</li>\n";
    return $output;
  }
  else {
    if (menu_in_active_trail($mid)) {
      return '<li class="active"><span>'. $menu['items'][$mid]['title'] ."</span></li>\n";
    }
    else {  
      return '<li>'. theme('menu_item', $mid) ."</li>\n";
    }
  }
} 

function phptemplate_menu_local_tasks() {
  $local_tasks = menu_get_local_tasks();
  $pid = menu_get_active_nontask_item();
  $output = '';

  if (count($local_tasks[$pid]['children'])) {
    $output .= "<div id=\"local-tasks\">\n";
    $output .= "  <ul class=\"primary\">\n";
    foreach ($local_tasks[$pid]['children'] as $mid) {
      $output .= theme('menu_local_task', $mid, menu_in_active_trail($mid));
    }
    $output .= "  </ul>\n";
    $output .= "</div>\n";
  }
  return $output;
} 
*/
?>