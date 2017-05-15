<div class="snappet-block">
    <div class="header">
        <a href="#">Snappet</a>
    </div>
    <div class="content">
        <div class="snappet-quick-add">
            <div class="snappet-quick-toggle text-center">
                <a href="#" class="snappet-quick-show">Legg til</a>
                <a href="#" class="snappet-quick-hide hidden">Avbryt</a>
            </div>
            <form class="snappet-quick-add-form hidden">
                <div class="form-group">
                    Title
                    <textarea class="title-input form-control"></textarea>
                </div>
                <div class="form-group">
                    <?php wp_editor('', 'tiny_quickadd_content', array(
                        'wpautop'       => 0,
                        'media_buttons' => 0,
                        'textarea_name' => 'content-input',
                        'textarea_rows' => 5,
                        'tabindex'      => 4,
                        'editor_css'    => '',
                        'editor_class'  => 'content-input form-control',
                        'teeny'         => 1,
                        'dfw'           => 0,
                        'tinymce'       => array(
                            'toolbar1' => 'bold, italic, link, unlink, undo, redo',
                            'toolbar2' => '',
                        ),
                        'quicktags'     => 0,
                        'drag_drop_upload' => false
                    )); ?>
                </div>
                <button type="submit" class="btn btn-default">Lagre</button>
            </form>
        </div>

        <div class="row">
            <?php sickfront_insert_post('snappet-0', 12); ?>
            <?php sickfront_insert_post('snappet-1', 12); ?>
            <?php sickfront_insert_post('snappet-2', 12); ?>
            <?php sickfront_insert_post('snappet-3', 12); ?>
            <?php sickfront_insert_post('snappet-4', 12); ?>
            <?php sickfront_insert_post('snappet-5', 12); ?>
            <?php sickfront_insert_post('snappet-6', 12); ?>
        </div>
    </div>
    <div class="footer">
        <a href="#">Se Flere</a>
    </div>
</div>
