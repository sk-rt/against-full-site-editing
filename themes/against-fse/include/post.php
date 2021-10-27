<?php

/***************************************************************

customize post type supports

 ***************************************************************/
/* ========================================

投稿機能のカスタム

======================================== */
/**
 * 投稿の機能を追加・削除
 */
function handle_post_suppurt()
{
    //remove from post
    remove_post_type_support('post', 'comments');
    remove_post_type_support('post', 'trackbacks');
    remove_post_type_support('post', 'post-formats');

    //remove from page
    remove_post_type_support('page', 'comments');
    remove_post_type_support('page', 'trackbacks');
}
add_action('init', 'handle_post_suppurt');

/**
 * 投稿からカテゴリ・タグの削除
 */
function remove_tax_from_post()
{
    global $wp_taxonomies;
    /*
     * 投稿機能から「タグ」を削除
     */
    if (!empty($wp_taxonomies['post_tag']->object_type)) {
        foreach ($wp_taxonomies['post_tag']->object_type as $i => $object_type) {
            if ($object_type == 'post') {
                unset($wp_taxonomies['post_tag']->object_type[$i]);
            }
        }
    }
    /*
     * 投稿機能から「カテゴリ」を削除
     */
    if (!empty($wp_taxonomies['category']->object_type)) {
        foreach ($wp_taxonomies['category']->object_type as $i => $object_type) {
            if ($object_type == 'post') {
                unset($wp_taxonomies['category']->object_type[$i]);
            }
        }
    }
    return true;
};
add_action('init', 'remove_tax_from_post');
