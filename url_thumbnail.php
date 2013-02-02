<?php
/*
  Plugin Name: Url Thumbnail
  Plugin URI: http://github.com/hirocaster/url_thumbnail
  Description: Make Thumbnail URL
  Version: 0.0.1
  Author: Hiroki OHTSUKA
  Author URI: http://hiroki.jp/
*/

function url_thumbnail_link($atts){
  extract(shortcode_atts(array(
                               'url'         => null,
                               'width'       => 400,
                               ), $atts));

  $upload_info = wp_upload_dir();
  $dir_path = $upload_info['path'] . '/url_thumbnail/';
  if(!file_exists($dir_path)){
    mkdir($dir_path);
  }

  $filename = make_screenshot($url, $width, $dir_path);
  $image_src = $upload_info['url'] . '/url_thumbnail/' . $filename;

  $img_tag = "<img src=\"{$image_src}\" />";

  return $img_tag;
}

add_shortcode('ut', 'url_thumbnail_link');

function make_screenshot($url, $width, $dir_path='./')
{
  $urlencode = urlencode($url);
  $width = intval($width);


  $filename = formating_filename($url, $width);
  $full_path = $dir_path . $filename;

  if(!file_exists($full_path))
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, "http://s.wordpress.com/mshots/v1/{$urlencode}?w={$width}");
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $data = curl_exec($ch);
      curl_close($ch);

      file_put_contents($full_path, $data);
    }
  return $filename;
}

function formating_filename($url, $width)
{
  $patterns = array();
  $patterns[0] = '/^https:\/\//';
  $patterns[1] = '/^http:\/\//';
  $patterns[2] = '/\/$/';
  $patterns[3] = '/\/+/';
  $patterns[4] = '/\?+/';
  $patterns[5] = '/\&+/';
  $patterns[6] = '/\=+/';
  $patterns[7] = '/#038;/';

  $replacements = array();
  $replacements[0] = '';
  $replacements[1] = '';
  $replacements[2] = '';
  $replacements[3] = '_';
  $replacements[4] = '_';
  $replacements[5] = '_';
  $replacements[6] = '_';
  $replacements[7] = '';

  return preg_replace($patterns, $replacements, $url) . "_{$width}" . '.jpg';
}
