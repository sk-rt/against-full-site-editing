# WordPress Theme for Minimalized Block Editor

[Full Site Editing](https://developer.wordpress.org/block-editor/getting-started/full-site-editing/)を掲げ進化し続けるブロックエディターと戦うための知見のメモ

対応バージョン: `WordPress 5.8.1`

## 動機

- フルサイト編集はもちろん、「記事を書く」目的にそんなに機能はいらない。
- ユーザーが迷わない様に最小限の機能に抑えたい。
- サイト全体のデザインシステムに合わせるため、ユーザーに余計なカラー選択 & レイアウトをさせたくない。

## スコープ

- ブロックエディターに関するの機能削除・変更

以下のものは含みません。

- カスタムブロックの追加
- ブロックの style に関するカスタマイズ
- classic editor やカスタムフィールドに関するカスタマイズ

## ローカルサーバー

wp-env を利用。

```sh
yarn && yarn wp:start
```

## カスタマイズするファイル / API

主に以下の方法でカスタマイズしていく。

- theme.json 
- PHP
- JavaSctipt
<!-- - [CSS](./themes/against-fse/admin-assets/js/custom-block-editor.js) -->

---

## theme.json

[ソースコード](./themes/against-fse/theme.json)

WordPress 5.8 から導入された API。  
テーマディレクトリ直下に `theme.json` 置くと設定なしに読み込まれる。  
公式のリファレンスでは以下が設定可能とのことだが、設定できる部分は現行では限定的。

> ブロックエディターの設定を定義する際、ネズミ算式に増えるテーマサポートフラグや代替方式の代わりに、theme.json ファイルでは理想の正しい方法が提供されます。例えば以下の設定が可能です。
>
> - ユーザーが利用可能なカスタマイズオプション。隠すカスタマイズオプション。
> - ユーザーが利用可能なデフォルトの色、フォントサイズ、等々
> - エディターのデフォルトレイアウトの定義。幅、利用可能な配置

今後成熟させていくとのことなので、できる事はなるべく theme.json で指定していく。  
配置可能なパスはテーマ直下のみ。よって Plugin 等では指定や上書き不可。

トップレベルのフィールドは以下。  
ブロックの機能に関するフィールドは `settings` フィールド。

```json
{
  "version": 1,
  "settings": {},
  "styles": {},
  "customTemplates": {},
  "templateParts": {}
}
```

現時点で詳細なフィールドのドキュメントが無いので、何ができて何ができないかは実装を見るか試してみるしかない。  
現時点で`setting`で有効そうなフィールドは以下。

```jsonc
{
  "version": 1,
  "settings": {
    "border": {
      "customColor": false,
      "customRadius": false,
      "customStyle": false,
      "customWidth": false
    },
    "color": {
      "background": false,
      "custom": false,
      "customDuotone": false,
      "customGradient": false,
      "duotone": [],
      "gradients": [],
      "link": false,
      "palette": [],
      "text": false
    },
    "layout": {
      "contentSize": "600px",
      "wideSize": "800px"
    },
    "spacing": {
      "customMargin": false,
      "customPadding": false,
      "units": ["px", "em", "rem", "vh", "vw"]
    },
    "typography": {
      "customFontSize": false,
      "customFontStyle": false,
      "customFontWeight": false,
      "customLineHeight": false,
      "customTextDecorations": false,
      "customTextTransforms": false,
      "dropCap": false,
      "fontFamilies": [],
      "fontSizes": []
    },
    "blocks": {
      // ブロックごとにsettingを上書き
      "core/button": {
        "border": {
          "customRadius": false
        }
      }
    }
  },
  "styles": {},
  "customTemplates": {},
  "templateParts": {}
}
```

cf.

- https://ja.wordpress.org/team/handbook/block-editor/how-to-guides/themes/theme-json/
- https://github.com/WordPress/WordPress/blob/b8e6a3c334d03b6b12bf1653375dda8cefb0d13e/wp-includes/class-wp-theme-json.php#L181

---

## PHP

[ソースコード](./themes/against-fse/include/editor.php)

function.php or pluginで実装する。

### `use_block_editor_for_post_type` フィルター

投稿タイプごとにブロックエディターを無効化。

```php
function disable_block_editor($use_block_editor, $post_type)
{
    // 固定ページではブロックエディターを使用しない
    if ($post_type === 'page') {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'disable_block_editor', 10, 2);
```

### `allowed_block_types_all` フィルター

使用するブロックを whitelist 形式で指定する。
`WP_Block_Type_Registry::get_instance()->get_all_registered()` で登録されている全ブロックを取得可能。

cf.

- https://github.com/WordPress/WordPress/blob/b8e6a3c334d03b6b12bf1653375dda8cefb0d13e/wp-includes/block-editor.php#L125

以下は`5.8.1` 時点での全ての core ブロックを category 順に並べ替えたもの。

```php

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
        'core/cover', // Cover
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

```

### `block_editor_settings_all` フィルター

全てのブロックエディター設定のフィルター。

cf.

- https://github.com/WordPress/WordPress/blob/b8e6a3c334d03b6b12bf1653375dda8cefb0d13e/wp-includes/block-editor.php#L370

```php
function filter_block_editor_settings($editor_settings, $editor_context)
{
    // align full / align wide を無効化
    $editor_settings['supportsLayout'] = false;
    // 画像編集の無効化
    $editor_settings['imageEditing'] = false;
    return $editor_settings;
}
add_filter('block_editor_settings_all', 'filter_block_editor_settings', 10, 2);
```

### `remove_theme_support()`

特定のテーマ機能の無効化。  
`disable-custom-colors`、`disable-custom-font-sizes` などは theme.json で代替可能。  
theme.json で設定できないもので有効そうな support は `core-block-patterns`

.cf

- [add_theme_support との後方互換性](https://ja.wordpress.org/team/handbook/block-editor/how-to-guides/themes/theme-json/#add_theme_support-%E3%81%A8%E3%81%AE%E5%BE%8C%E6%96%B9%E4%BA%92%E6%8F%9B%E6%80%A7)

```php
function remove_block_editor_supports()
{
    // ブロックエディターのパターン機能を全て無効化
    remove_theme_support('core-block-patterns');
}
add_action('init', 'remove_block_editor_supports');
```

### `unregister_block_pattern()`

ブロックパターンを個別に削除。

＊ `WP_Block_Patterns_Registry::get_instance()->get_all_registered()` で全パターンが取得できるはずだが、なぜか一部のみしか取得できない。

```php
function remove_block_editor_pattern()
{
    // 個別にパターンを削除
    unregister_block_pattern( 'core/xxx' );

    $all_patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    foreach($all_patterns as $pattern){
        // coreパターンのみ削除 ＊現時点で一部のみしか削除できない。
        if(strpos( $pattern['name'],'core/' ) === 0){
            unregister_block_pattern( $pattern['name'] );
        };
    }
}
add_action('init', 'remove_block_editor_pattern');
```

### `image_size_names_choose` フィルター

エディターで選択できる画像サイズのフィルター

```php
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
```

### `block_categories_all` フィルター

ブロックカテゴリのフィルター。  
カテゴリを削除すると、属するブロックは Uncategorized カテゴリに入り、ブロックごと削除される訳ではない。  
カテゴリ名を変えたい時などに使うと良いかも。

```php
function filter_block_categories($block_categories, $editor_context)
{
    // 全カテゴリスラッグ
    // 'text', 'media', 'design', 'widgets', 'theme', 'embed', 'reusable', 'text', 'media', 'design', 'widgets', 'theme', 'embed', 'reusable'
    $remove_categories = ['theme']; // 削除するカテゴリ
    $filterd_categories = array_filter($block_categories, function ($category) use ($remove_categories) {
        return !in_array($category['slug'], $remove_categories, true);
    });
    return $filterd_categories;
}

add_filter('block_categories_all', 'filter_block_categories', 10, 2);
```

---

## JavaScript

[ソースコード](./themes/against-fse/admin-assets/js/custom-block-editor.js)

まずは以下の方法で js ファイルを読み込む。  
＊JSX を使いたい時など、npm 経由で実装する時は別途ビルドが必要。

```php
function enqueue_cutomize_block_editor_assets()
{
    wp_enqueue_script('custom-editor-script', get_stylesheet_directory_uri() . '/admin-assets/js/custom-block-editor.js', array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'), '1.0', false);
}
add_action('enqueue_block_editor_assets', 'enqueue_cutomize_block_editor_assets');
```

### `wp.richText.unregisterFormatType()`

RichText コンポーネントのフォーマットタイプの削除。  
`wp.data.select("core/rich-text").getFormatTypes()` で全てのフォーマットタイプが取得できる。  
現時点で全てのフォーマットタイプは以下。

```js
wp.domReady(() => {
  // フォーマットタイプの削除
  wp.richText.unregisterFormatType("core/bold"); // Bold
  wp.richText.unregisterFormatType("core/code"); // Inline code
  wp.richText.unregisterFormatType("core/image"); // Inline image
  wp.richText.unregisterFormatType("core/italic"); // Italic
  wp.richText.unregisterFormatType("core/link"); // Link
  wp.richText.unregisterFormatType("core/strikethrough"); // Strikethrough
  wp.richText.unregisterFormatType("core/underline"); // Underline
  wp.richText.unregisterFormatType("core/text-color"); // Text color
  wp.richText.unregisterFormatType("core/subscript"); // Subscript
  wp.richText.unregisterFormatType("core/superscript"); // Superscript
  wp.richText.unregisterFormatType("core/keyboard"); // Keyboard input
});
```

### `wp.blocks.unregisterBlockStyle()`

ブロックスタイルを削除。  
＊サイドバーブロックパネルの「styles」。CSS style の事ではない。

```js
wp.domReady(() => {
  // image
  wp.blocks.unregisterBlockStyle("core/image", "rounded");
  wp.blocks.unregisterBlockStyle("core/image", "default");
  // quote
  wp.blocks.unregisterBlockStyle("core/quote", "default");
  wp.blocks.unregisterBlockStyle("core/quote", "large");
  // button
  wp.blocks.unregisterBlockStyle("core/button", "fill");
  wp.blocks.unregisterBlockStyle("core/button", "outline");
  // pullquote
  wp.blocks.unregisterBlockStyle("core/pullquote", "default");
  wp.blocks.unregisterBlockStyle("core/pullquote", "solid-color");
  // separator
  wp.blocks.unregisterBlockStyle("core/separator", "default");
  wp.blocks.unregisterBlockStyle("core/separator", "wide");
  wp.blocks.unregisterBlockStyle("core/separator", "dots");
  // table
  wp.blocks.unregisterBlockStyle("core/table", "regular");
  wp.blocks.unregisterBlockStyle("core/table", "stripes");
  // social-links
  wp.blocks.unregisterBlockStyle("core/social-links", "default");
  wp.blocks.unregisterBlockStyle("core/social-links", "logos-only");
  wp.blocks.unregisterBlockStyle("core/social-links", "pill-shape");
});
```

`wp.blocks.getBlockTypes()` で全てのブロックが取得できるので、全ブロックの styles の削除は以下でできる。

```js
wp.domReady(() => {
  // 全てのブロックスタイルを削除
  const allBlocks = wp.blocks.getBlockTypes();
  allBlocks.forEach((block) => {
    if (block.styles.length === 0) {
      return;
    }
    block.styles.forEach((style) => {
      wp.blocks.unregisterBlockStyle(block.name, style.name);
    });
  });
});
```

### `wp.blocks.unregisterBlockVariation()`

ブロックバリエーションの削除。  
`core/enmbed` など大量にあるバリエーションを整理できる。

```js
// "core/embed"のバリエーションを以下を除いて削除
wp.domReady(() => {
  const allowedEmbedVariation = ["youtube", "vimeo", "twitter", "wordpress"];
  wp.blocks.getBlockVariations("core/embed").forEach((variation) => {
    if (allowedEmbedVariation.indexOf(variation.name) !== -1) return;
    wp.blocks.unregisterBlockVariation("core/embed", variation.name);
  });
});
```

styles と同様に全ブロックで `blocks.variations` を持つブロックを抽出してバリエーションを削除。

```js
const allBlocks = wp.blocks.getBlockTypes();
allBlocks.forEach((block) => {
  if (block.variations.length === 0) {
    return;
  }
  block.variations.foreach((variation) => {
    wp.blocks.unregisterBlockVariation(block.name, variation.name);
  });
});
```

## `blocks.registerBlockType` フィルター

`wp.hooks` の `blocks.registerBlockType` フィルターでブロックの設定を上書き可能。  
試してみて有効なのは supports の上書きのみ。ほとんどはtheme.jsonなどで設定可能。

```js
wp.hooks.addFilter(
    "blocks.registerBlockType",
    "app/custom-block-type-filter",
    (settings, name) => {
      if (name === "core/heading") {
        if (settings.supports) {
          // AdvencedパネルのHTMLアンカー設定機能の削除
          settings.supports.anchor = false;
        }
      }
      return settings;
    }
  );
```

## その他ツールバー・サイドパネルの削除

align 系のツールバーボタンや、上記のstylesや supports系（color/fonts size）以外で実装されている個別の InspectorControls パネルなどは、現状では効率的に削除する API なし。

実装を完全に上書きする形になるが、[`editor.BlockEdit` フィルター](https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#editor-blockedit) でブロックコンポーネントの`edit()`を上書きするしかない。
