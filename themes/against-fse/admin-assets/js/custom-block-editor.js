/* -------------------------
 
customize block editor
 
----------------------------*/

wp.domReady(() => {
  removeBlockVariatons();
  removeFormatTypes();
  removeBlockStyles();
});
// filterBlockSetting();


/**
 *  Remove BlockVariatons
 */
function removeBlockVariatons() {
  if (!wp || !wp.blocks) {
    return;
  }
  // variationを持つ全てのブロックを抽出
  // const allBlocks = wp.blocks.getBlockTypes();
  // allBlocks.forEach((block) => {
  //   if (block.variations.length === 0) {
  //     return;
  //   }
  //   console.log(block);
  // });
  /**
   * 不要なcore/embedのBlockVariationを削除
   */
  const allowedEmbedVariation = ["youtube", "vimeo", "twitter", "wordpress"];
  wp.blocks.getBlockVariations("core/embed").forEach((variation) => {
    if (allowedEmbedVariation.indexOf(variation.name) !== -1) return;
    wp.blocks.unregisterBlockVariation("core/embed", variation.name);
  });
  /**
   * 不要なcore/columnsのBlockVariationを削除
   */
  const allowedColumnsVariation = ["two-columns-equal"];
  wp.blocks.getBlockVariations("core/columns").forEach((variation) => {
    if (allowedColumnsVariation.indexOf(variation.name) !== -1) return;
    wp.blocks.unregisterBlockVariation("core/columns", variation.name);
  });
}

/**
 * Remove FormatTypes
 */
function removeFormatTypes() {
  if (!wp || !wp.richText) {
    return;
  }

  // 全てのフォーマットタイプを削除
  // const allFormatTypes = wp.data.select("core/rich-text").getFormatTypes();
  // allFormatTypes.forEach((formatType) => {
  //   wp.richText.unregisterFormatType(formatType.name);
  // });

  // 個別に削除
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
}

/**
 * Remove BlockStyles
 */
function removeBlockStyles() {
  if (!wp || !wp.blocks) {
    return;
  }
  // 全てのブロックスタイルを削除
  // const allBlocks = wp.blocks.getBlockTypes();
  // allBlocks.forEach((block) => {
  //   if (block.styles.length === 0) {
  //     return;
  //   }
  //   block.styles.forEach((style) => {
  //     wp.blocks.unregisterBlockStyle(block.name, style.name);
  //   });
  // });

  // ブロック別にスタイル削除
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
}



/**
 * [WIP] filtering block settings
 * supportsの上書きでanchorなどは制御できる。
 */
 function filterBlockSetting() {
  wp.hooks.addFilter(
    "blocks.registerBlockType",
    "app/custom-block-type-filter",
    (settings, name) => {
      if (name === "core/heading") {
        if (settings.supports) {
          // settings.supports.align = false;
          // アンカーの削除
          settings.supports.anchor = false;
        }
      }
      return settings;
    }
  );
}