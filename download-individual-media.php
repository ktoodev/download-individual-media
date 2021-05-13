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

    $script = <<<EOS
        (function() {
        var parent = document.querySelector('.setting[data-setting="url"]');
        var button = document.querySelector('.download-attachment-button');
        parent.appendChild(button);

        document.querySelector('.compat-field-download-file-field').style.display = "none";
      })();
EOS;

    $form_fields['download-file-field'] = array(
        'label' => 'Download',
        'input' => 'html',
        'html' => '<button type="button" style="margin-left: calc( 35% - 1px ); margin-top: 3pt;" class="button button-small download-attachment-button"><a style="text-decoration:none" href="' . wp_get_attachment_url($post->ID) . '" download="' . $post->post_title . '">Download file</a></button>' . '<script>' . $script . '</script>',
    );
    
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', __NAMESPACE__ . '\download_in_fields_to_edit', 10, 2 );