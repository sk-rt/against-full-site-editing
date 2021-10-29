<?php

/***************************************************************

Editor

 ***************************************************************/


/**
 * Remove block-library/style.min.css
 */
function dequeue_block_library_css()
{
    // 投稿詳細ページのみblock-library/style.min.cssを読み込み
    if (!is_singular('post')) {
        wp_dequeue_style('wp-block-library');
    }
}
add_action('wp_enqueue_scripts', 'dequeue_block_library_css');

/**
 *  Load JS/CSS for block editor
 */
function enqueue_cutomize_block_editor_assets()
{
    $temp_url = get_stylesheet_directory_uri();
    $temp_path = get_stylesheet_directory();
    // JS
    $js_path = '/admin-assets/js/custom-block-editor.js';
    wp_enqueue_script(
        'custom-editor-script',
        $temp_url .  $js_path,
        array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
        get_filetime($temp_path . $js_path),
        false
    );
    // CSS
    $css_path = '/admin-assets/css/custom-editor.css';
    wp_enqueue_style(
        'custom-editor-style',
        $temp_url .  $css_path,
        ['wp-edit-blocks'],
        get_filetime($temp_path . $css_path)
    );
}
add_action('enqueue_block_editor_assets', 'enqueue_cutomize_block_editor_assets');



/**
 * Disable block editor
 * @param boolean $use_block_editor
 * @param string $post_type
 * @return boolean
 */
function disable_block_editor($use_block_editor, $post_type)
{
    // 固定ページではブロックエディターを使用しない
    if ($post_type === 'page') {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'disable_block_editor', 10, 2);


/**
 * 使用するブロックを指定
 */
function custom_allowed_block_types($allowed_block_types)
{
    $allowed_block_types = array(
        // ------- design -------
        'core/button', // Button
        'core/buttons', // Buttons
        'core/columns', // Columns
        'core/group', // Group
        'core/more', // More
        'core/nextpage', // Page Break
        'core/post-template', // Post Template
        'core/post-terms', // Post Terms
        'core/query-pagination', // Query Pagination
        'core/query-pagination-next', // Query Pagination Next
        'core/query-pagination-numbers', // Query Pagination Numbers
        'core/query-pagination-previous', // Query Pagination Previous
        'core/query-title', // Query Title
        'core/separator', // Separator
        'core/site-tagline', // Site Tagline
        'core/site-title', // Site Title
        'core/spacer', // Spacer
        'core/text-columns', // Text Columns (deprecated)
        // ------ embed -------
        'core/embed', // Embed
        // -------layout -------
        'core/site-logo', // Site Logo
        // ------ media -------
        'core/audio', // Audio
        // 'core/cover', // Cover
        'core/file', // File
        'core/gallery', // Gallery
        'core/image', // Image
        'core/media-text', // Media & Text
        'core/video', // Video
        // ---------usable -------
        'core/block', // Reusable block
        // ------ text -------
        'core/code', // Code
        'core/column', // Column
        'core/freeform', // Classic
        'core/heading', // Heading
        'core/list', // List
        'core/missing', // Unsupported
        'core/paragraph', // Paragraph
        'core/preformatted', // Preformatted
        'core/pullquote', // Pullquote
        'core/quote', // Quote
        'core/table', // Table
        'core/verse', // Verse
        // ------ theme -------
        'core/loginout', // Login/out
        'core/post-content', // Post Content
        'core/post-date', // Post Date
        'core/post-excerpt', // Post Excerpt
        'core/post-featured-image', // Post Featured Image
        'core/post-title', // Post Title
        'core/query', // Query Loop
        // -------- widgets -------
        'core/archives', // Archives
        'core/calendar', // Calendar
        'core/categories', // Categories
        'core/html', // Custom HTML
        'core/latest-comments', // Latest Comments
        'core/latest-posts', // Latest Posts
        'core/legacy-widget', // Legacy Widget
        'core/page-list', // Page List
        'core/rss', // RSS
        'core/search', // Search
        'core/shortcode', // Shortcode
        'core/social-link', // Social Icon
        'core/social-links', // Social Icons
        'core/tag-cloud', // Tag Cloud
    );
    return $allowed_block_types;
}
add_filter('allowed_block_types_all', 'custom_allowed_block_types');


/**
 * ブロックエディターの設定の上書き
 * @param array $editor_settings
 * @param array $editor_context
 * @return array
 */
function filter_block_editor_settings($editor_settings, $editor_context)
{

    // align full / align wide を無効化
    $editor_settings['supportsLayout'] = false;
    // 画像編集の無効化
    $editor_settings['imageEditing'] = false;

    return $editor_settings;
}
add_filter('block_editor_settings_all', 'filter_block_editor_settings', 10, 2);

/**
 * ブロックカテゴリのフィルター
 * カテゴリを削除すると、属するブロックは Uncategorized カテゴリに入る。
 * ブロックごと削除される訳ではない。
 *
 * @param array $block_categories 全カテゴリの配列
 * @param array $editor_context
 * @return array
 */
function filter_block_categories($block_categories, $editor_context)
{
    // 全カテゴリスラッグ
    // 'text', 'media', 'design', 'widgets', 'theme', 'embed', 'reusable', 'text', 'media', 'design', 'widgets', 'theme', 'embed', 'reusable'
    $remove_categories = ['some-categoriy']; // 削除するカテゴリ
    $filterd_categories = array_filter($block_categories, function ($category) use ($remove_categories) {
        return !in_array($category['slug'], $remove_categories, true);
    });
    return $filterd_categories;
}

add_filter('block_categories_all', 'filter_block_categories', 10, 2);

/**
 * テーマ機能の無効化
 */
function remove_block_editor_supports()
{
    // ブロックエディターのパターンを削除
    remove_theme_support('core-block-patterns');

    // 個別にパターンの削除
    $patterns = get_all_sorted_patterns();
    foreach ($patterns as $pattern) {
        if (strpos($pattern['name'], 'core/') === 0) {
            unregister_block_pattern($pattern['name']);
        };
    }
}
add_action('init', 'remove_block_editor_supports', 10);


/**
 * エディターの画像サイズのリスト
 */
function customize_image_sizes($size_names)
{
    // default
    $size_names = array(
        'thumbnail' => __('Thumbnail'),
        'medium'    => __('Medium'),
        'large'     => __('Large'),
        'full'      => __('Full Size'),
    );
    return $size_names;
}
add_filter('image_size_names_choose', 'customize_image_sizes');
