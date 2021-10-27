/* -------------------------
 
customize block editor
 
----------------------------*/

wp.domReady(() => {
  removeBlockVariatons();
  removeFormatTypes();
  removeBlockStyles();
});
filterBlockSetting();

/**
 * [WIP] filtering block settings
 */
function filterBlockSetting() {
  wp.hooks.addFilter(
    "blocks.registerBlockType",
    "app/custom-filter",
    (settings, name) => {
      // console.log(name);
      if (name === "core/heading") {
        // console.log(settings);
        if (settings.supports) {
          settings.supports.align = false;
        }
      }
      if (name === "core/image") {
        // console.log(settings);
        if (settings.supports) {
          settings.supports.align = false;
        }
      }
      if (name === "core/button") {
        console.log(settings);
        if (settings.supports) {
          // settings.supports.alignWide = false;
          settings.supports.color = false;
        }
      }
      if (name === "core/buttons") {
        console.log(settings);
        // settings.styles = [];
        // if (settings.supports) {
        //   // settings.supports.alignWide = false;
        //   settings.supports.color = false;
        // }
      }

      return settings;
    }
  );
}

/**
 *  [WIP] Remove BlockVariatons
 */
function removeBlockVariatons() {
  if (!wp || !wp.blocks) {
    return;
  }
  const allowedEmbedVariation = [
    "youtube",
    "vimeo",
    "twitter",
    "facebook",
    "instagram",
    "wordpress",
  ];
  wp.blocks.getBlockVariations("core/embed").forEach((variation) => {
    if (allowedEmbedVariation.indexOf(variation.name) !== -1) return;
    wp.blocks.unregisterBlockVariation("core/embed", variation.name);
  });
}

/**
 * Remove FormatTypes
 */
function removeFormatTypes() {
  if (!wp || !wp.richText) {
    return;
  }
  /* 
   // 全てのフォーマットタイプ
   const allFormatTypes = wp.data.select("core/rich-text").getFormatTypes();
   allFormatTypes.forEach((formatType) => {
     console.log(formatType.name, formatType.title); 
   });
  */
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
