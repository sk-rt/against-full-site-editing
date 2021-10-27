<?php
/***************************************************************


utility


 ***************************************************************/

/**
 * Logファイルに出力
 */
function debug_log($var)
{
    \error_log(var_export($var, true) . "\n", 3, get_stylesheet_directory() . '/log/debug.log');
};
/**
 * ファイルの更新日を取得
 * @param string $filepath
 * @return void
 */
function get_filetime($filepath)
    {
        if (file_exists($filepath)) {
            return filemtime($filepath);
        } else {
            return null;
        }
    };
/**
 * カテゴリ・ブロック名でソートした全ブロック
 * @return array
 */
function get_all_sorted_blocks()
{
    $all_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

    usort($all_blocks, function ($right, $left) {
        return $right->category === $left->category ?
            strcmp($right->name, $left->name) :
            strcmp($right->category, $left->category);
    });
    return $all_blocks;
}


/**
 * カテゴリ・ブロック名でソートした全パターン
 * [NOTE] なぜかqueryカテゴリのみしか取得できない。button,columns,gelleryなどは取得できず。
 * @return array
 */
function get_all_sorted_patterns()
{
    $all_patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
  
    usort($all_patterns, function ($right, $left) {
        return $right['categories'][0] === $left['categories'][0] ?
            strcmp($right['name'], $left['name']) :
            strcmp($right['categories'][0], $left['categories'][0]);
    });
    return $all_patterns;
}
