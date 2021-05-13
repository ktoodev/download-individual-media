<?php
/*
Plugin Name: Download Individual Media
Description: Add download options for individual media
Version: 0.0.1
Author: David Purdy
Author URI: https://dpurdy.com
Text Domain: download-individual-media
License: GPLv2
*/

namespace DownloadIndividualMedia;

/**
 * Add download button to media edit screen
 */
function download_in_fields_to_edit ($form_fields, $post) {

    // script to move the button from the fields area up to the URL area (below the 'copy URL' button) since there's no hook to insert it there
    $script = <<<EOS
        (function() {
        var parent = document.querySelector('.setting[data-setting="url"]');
        var button = document.querySelector('.download-attachment-button');
        parent.appendChild(button);

        document.querySelector('.compat-field-download-file-field').style.display = "none";
      })();
EOS;

    // add the "field" who's only purpose is the button
    $form_fields['download-file-field'] = array(
        'label' => 'Download',
        'input' => 'html',
        'html' => sprintf(
            '<button type="button" style="margin-left: calc( 35% - 1px ); margin-top: 3pt;" class="button button-small download-attachment-button"><a style="text-decoration:none" href="%s" download="%s">Download file</a></button><script>%s</script>',
            wp_get_attachment_url($post->ID),
            $post->post_title,
            $script
        ),
    );
    
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', __NAMESPACE__ . '\download_in_fields_to_edit', 10, 2 );


/**
 * Add download link in editor for shodcode
 */
function audio_shortcode_download_link($output, $tag, $attr){
    if( ! is_admin() || 'audio' != $tag){ //make sure it is the right shortcode
      return $output;
    }

    $filetypes = array ('mp3', 'm4a', 'ogg', 'wav', 'wma');
    $download_list = array();
    foreach ($attr as $key => $value) {
        if (in_array($key, $filetypes)) {
            $download_list[] = '<a class="audio-download-link" href="' . $value . '" download="' . basename($value) . '">Download ' . $key . '</a>';
        }
        elseif (strtolower($key) == 'src') {
            $path_info = pathinfo ($value);
            $download_list[] = '<a class="audio-download-link" href="' . $value . '" download="' . basename($value) . '">Download ' . $path_info['extension'] . '</a>';
        }
    }

    return $output . '<span class="audio-download-links" style="font-size:11pt; text-align: right; width: 100%; display: block;">' . implode(' | ', $download_list) . '</span>';
  }
  add_filter( 'do_shortcode_tag', __NAMESPACE__ . '\audio_shortcode_download_link',10,3);
