<?php
/*
Plugin Name: Download Individual Media
Description: Add download options for individual media
Version: 0.0.2
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
            '<a class="download-attachment-button" style="margin-left: calc( 35%% - 1px ); margin-top: 4pt;display: inline-block;" target="_blank" href="%s" download="%s">%s</a><script>%s</script>',
            wp_get_attachment_url($post->ID),
            $post->post_title,
            basename(\wp_get_attachment_url($post->ID)),
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
        $path_info = pathinfo ($value);
        if (in_array($key, $filetypes)) {
            $download_list[] = '<a class="audio-download-link" target="_blank" href="' . $value . '" download="' . basename($value) . '">' . $path_info['basename'] . '</a>';
        }
        elseif (strtolower($key) == 'src') {
            $download_list[] = '<a class="audio-download-link" target="_blank" href="' . $value . '" download="' . basename($value) . '">' . $path_info['basename'] . '</a>';
        }
    }

    return $output . '<span class="audio-download-links" style="font-size:11pt; text-align: right; width: 100%; display: block;">' . implode(' | ', $download_list) . '</span>';
  }
  add_filter( 'do_shortcode_tag', __NAMESPACE__ . '\audio_shortcode_download_link',10,3);
