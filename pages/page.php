<?php

function sickfront_insert_post($key, $width = 12) {
    list($stack_name, $index, $big) = explode('-', $key);
    $is_big = ($big == 'big');

    $article_class = $is_big ? 'article-medium' : 'article-small';
    $edit_fontsize = $is_big ? 'true' : 'false';

    $edit_content = ($stack_name == 'snappet') ? 'true' : 'false';
    $edit_image = ($stack_name == 'snappet') ? 'false' : 'true';

    echo <<<"OEF"
<div class="col-sm-{$width}">
    <div class="article-wrapper stack-{$stack_name}">
        <div data-dropzone-area="{$stack_name}" data-dropzone-index="{$index}" data-editable-font-size="{$edit_fontsize}" data-editable-content="{$edit_content}" data-editable-image="{$edit_image}" class="dropzone article {$article_class}"></div>
        <div class="close-button"></div>
    </div>
</div>
OEF;
}

function sickfront_insert_place($text, $width = 12, $height = null) {
    $height_class = ($height) ? " place-height-x{$height}" : '';
    echo <<<"OEF"
<div class="col-sm-{$width}">
    <div class="well text-center place{$height_class}">
        {$text}
    </div>
</div>
OEF;
}

function sickfront_render_section_styles($cat) {
    $cat = get_category_by_slug($cat);

    if (!$cat) {
        $cat = get_category_by_slug('sysla');
        if (!$cat) {
            sickfront_render_styles_for_section();
        }
    }

    $color = get_field('category_color', $cat->taxonomy . '_' . $cat->term_id);

    $color = ($color) ? $color : '#000';

    echo <<<EOF
<style>
    .snappet-block .header, .snappet-block .footer {
        background-color: {$color};
      }
</style>
EOF;
}
?>

<?php sickfront_render_section_styles($this->slug) ?>
<div class="sickfront">
    <div class="sickfront-wrapper">
        <div class="sickfront-wrapper-inner">
            <div class="sidebar-column">
                <?php include SICKFRONT_PLUGIN_PATH . '/pages/parts/sidebar/sidebar.php'; ?>
            </div>
            <div class="preview-column">
                <?php include SICKFRONT_PLUGIN_PATH . '/pages/parts/layout-control/main.php'; ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>