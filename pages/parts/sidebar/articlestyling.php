<div class="sickfront-live">
    <div class="row">
        <div class="col-xs-12">
            <div class="sickfront-label"><?php _e('Article Styling', 'sickfront'); ?></div>
            <div class="article-styling-wrapper">

                <div class="block-hidden">
                    <div class="article-settings">
                        <i>Select an article to edit.</i>
                    </div>
                </div>

                <div class="block-shown hidden">
                    <div class="article-head-control">
                        <div class="pull-left">
                            <a class="preview-link" href="#" target="_blank"><?php _e('Go to Preview', 'sickfront'); ?></a>
                        </div>
                        <div class="pull-right">
                            <a class="edit-link"  href="#" target="_blank"><?php _e('Go to Edit', 'sickfront'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="article-settings">
                        <form action="#" data-form="live-edit-form">
                            <div class="form-group grey-background">
                                <div class="live-elements">
                                    <div class="live-header">Elements</div>
                                    <ul class="list-inline">
                                        <li class="live-image-element">
                                            <label>
                                                <input type="checkbox" name="live_elements[]" value="image" />
                                                <?php _e('Image', 'sickfront'); ?>
                                            </label>
                                        </li>
                                        <li class="live-title-element">
                                            <label>
                                                <input type="checkbox" name="live_elements[]" value="title" />
                                                <?php _e('Title', 'sickfront'); ?>
                                            </label>
                                        </li>
                                        <li class="live-content-element">
                                            <label>
                                                <input type="checkbox" name="live_elements[]" value="content" />
                                                <?php _e('Content', 'sickfront'); ?>
                                            </label>
                                        </li>
                                    </ul>
                                </div>

                                <div class="live-image">
                                    <div class="live-header">Image</div>
                                    <input type="hidden" name="live_image" />

                                    <div class="live_image_preview"></div>
                                    <div class="text-right">
                                        <a href="#" class="live_image_remove pull-left">Remove Image</a>
                                        <input type="button" name="upload-btn" class="live_image_upload" value="Upload Image">
                                    </div>
                                </div>

                                <div class="live-fontsize">
                                    <div class="live-header">Title Font Size</div>
                                    <input type="text" id="live_fontsize" class="live_fontsize" name="live_fontsize" data-slider-id='title-size-slider' data-slider-min="20" data-slider-max="100" data-slider-step="1" data-slider-value="36" />
                                </div>

                                <div class="live-title">
                                    <div class="live-header">Title</div>
                                    <textarea name="live_title" rows="3" class="form-control"></textarea>
                                </div>

                                <div class="live-content">
                                    <div class="live-header">Content</div>
                                    <?php wp_editor('', 'tiny_live_content', array(
                                        'wpautop'       => false,
                                        'media_buttons' => 0,
                                        'textarea_name' => 'live_content',
                                        'textarea_rows' => 5,
                                        'tabindex'      => 0,
                                        'editor_css'    => '',
                                        'editor_class'  => '',
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
                            </div>
                        </form>
                        <div class="text-right">
                            <a class="cancel-link pull-left" href="#"><?php _e('Cancel', 'sickfront'); ?></a>
                            <a class="close-link" href="#"><?php _e('Save as Draft', 'sickfront'); ?></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
