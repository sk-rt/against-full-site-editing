<?php

/**
 * remove parent theme setting
 */
function remove_parent_theme_action()
{
    remove_action('init', 'twenty_twenty_one_register_block_styles');
    remove_action('enqueue_block_editor_assets', 'twentytwentyone_block_editor_script');
    remove_action('after_setup_theme', 'twenty_twenty_one_setup');
}
add_action('after_setup_theme', 'remove_parent_theme_action', 9);

/**
 * utility
 */
require get_stylesheet_directory() .'/include/utility.php';

/**
 * setup post support
 */
require get_stylesheet_directory() .'/include/post.php';

/**
 * block editor
 */
require get_stylesheet_directory() .'/include/editor.php';
