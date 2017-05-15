<div class="sickfront-layout-control">
    <div class="sickfront-fixed-header-wrapper">
        <div class="sickfront-fixed-header">
            <div class="alert-block hidden">
                <div class="pull-left">
                    <i>Please do not forget to click Save button to publish the changes</i>
                </div>
                <div class="alert-close-button pull-right"></div>
                <div class="clearfix"></div>
            </div>
            <div class="sickfront-head">
                <div class="pull-right">
                    <button type="button" id="revert-btn" class="btn btn-default"><?php _e('Revert', 'sickfront'); ?></button>
                    <button type="button" id="save-btn" class="btn btn-primary"><?php _e('Save', 'sickfront'); ?></button>
                </div>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#desktop" aria-controls="desktop" role="tab" data-toggle="tab"><?php _e('Desktop', 'sickfront'); ?></a></li>
                <li role="presentation"><a href="#tablet" aria-controls="tablet" role="tab" data-toggle="tab"><?php _e('Tablet', 'sickfront'); ?></a></li>
                <li role="presentation"><a href="#mobile" aria-controls="mobile" role="tab" data-toggle="tab"><?php _e('Mobile', 'sickfront'); ?></a></li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="desktop">
            <div class="sickfront-dropzone-layout">
                <div class="desktop-layout">
                    <?php include SICKFRONT_PLUGIN_PATH . '/pages/parts/layout-control/' . $this->slug . '/layout-desktop.php'; ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane pull-left" id="tablet">
            <div class="sickfront-dropzone-layout pull-left">
                <div class="tablet-layout">
                    <?php include SICKFRONT_PLUGIN_PATH . '/pages/parts/layout-control/' . $this->slug . '/layout-tablet.php'; ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane pull-left" id="mobile">
            <div class="sickfront-dropzone-layout pull-left">
                <div class="mobile-layout">
                    <?php include SICKFRONT_PLUGIN_PATH . '/pages/parts/layout-control/' . $this->slug . '/layout-mobile.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>
