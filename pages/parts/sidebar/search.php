<div class="row">
    <div class="col-xs-12">
        <div class="sickfront-label sickfront-label-collapsable search-label" data-toggle="collapse" data-target="#sickfront-post-search-wrapper"><?php _e('Article Search', 'sickfront'); ?><span class="caret"></span></div>
        <div id="sickfront-post-search-wrapper" class="sickfront-post-search-wrapper expand">
            <div class="sickfront-post-search">
                <div class="search-wrapper">
                    <form action="#" data-form="search-form">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" id="sickfront-posts-search" placeholder="<?php _e('Search...', 'sickfront') ?>" />
                            <span class="input-group-btn">
                                <button id="search-btn" class="btn btn-default" type="button"><?php _e('Filter', 'sickfront') ?></button>
                            </span>
                        </div>
                        <div class="filters">
                            <div class="form-group">
                                <button class="form-control" data-toggle="collapse" data-target="#filters">Filters<span class="caret"></span></button>
                                <div id="filters" class="collapse">
                                    <?php
                                        $args = array(
                                            'orderby'			=> 'name',
                                            'show_option_all'   => __('All categories', 'sickfront'),
                                            'name'              => 'catid',
                                            'class'                => 'dropdown form-control',
                                        );
                                        wp_dropdown_categories( apply_filters('sickfront_dropdown_categories', $args) );
                                    ?>
                                    <?php
                                        $args = array(
                                            'show_option_all' 	=> __('All authors', 'sickfront'),
                                            'name' 				=> 'author',
                                            'id' 				=> 'sickfront-posts-author',
                                            'who'				=> 'authors',
                                            'show'              => 'display_name',
                                            'class'                => 'dropdown form-control'
                                        );
                                        wp_dropdown_users( $args );
                                    ?>

                                    <select name="area" id="sickfront-area" class="dropdown form-control">
                                        <option value="main" selected="selected">Main Area</option>
                                        <option value="snappet">Snappet Area</option>
                                    </select>
                                    <?php
                                    ?>
                                </div>
                            </div>
                            <!--img src="<?php echo SICKFRONT_PLUGIN_URL .'/images/ajax-loader-trans.gif'; ?>" class="ajax-loader" / -->
                        </div>
                    </form>
                </div>
                <div class="search-result">
                    <div class="search-result-count"></div>
                    <div class="search-result-list"></div>
                    <div class="sickfront-pagination-wrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>
