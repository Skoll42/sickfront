'use strict';

(function($) {

    function _isElementOn(element, post) {
        if (post.fronted_elements === null) {
            return true;
        }
        return $.inArray(element, post.fronted_elements) != -1;
    }

    function _nl2br(str) {
        str = str || '';
        str = str.replace(new RegExp('<br ?\/?>', 'g'), '');
        return str.replace(new RegExp('\r?\n', 'g'), '<br />');
    }

    var Filter = (function() {
        var POSTS = [];
        var LIMIT = 10;

        function init() {
            $('form[data-form="search-form"]').on('submit', function (e) {
                e.preventDefault();
                _fetchPosts(1);
            });

            $('form[data-form="search-form"] #search-btn').click(function () {
                $(this).submit();
            });

            $(document).on('sickfront.dropzone.selectPost', function (e, post, settings) {
                if (_formGetData().area != settings.area) {
                    _fetchPosts(1, {area: settings.area}, true);
                }
            });


            _fetchPosts(1);
        }

        function getPostById(id) {
            for (var i = 0; i < POSTS.length; i++) {
                var post = POSTS[i];
                if (post.ID == id) {
                    return post;
                }
            }
        }

        function _convertAreaToPostType(area) {
            switch(area) {
                case 'snappet':
                    return 'snappet';
                    break;
                case 'main':
                default:
                    return ['post', 'pressrelease', 'podcast'];
                    break;
            }
        }

        function _fetchPosts(page, additionalParams, reset) {
            page = page || 1;
            additionalParams = additionalParams || {};
            reset = reset || false;

            var formDataObj = (reset) ? {} : _formGetData();
            
            if (additionalParams.area) {
                additionalParams.post_type = _convertAreaToPostType(additionalParams.area);
            }
            if (formDataObj.area) {
                formDataObj.post_type = _convertAreaToPostType(formDataObj.area);
            }

            var defaultParams = {
                catid: 0,
                author: 0,
                search: '',
                post_type: 'post',
                offset: (page - 1) * LIMIT,
                limit: LIMIT,
                action: 'sickfront_filter_posts',
                _ajax_nonce: SickfrontJS.nonce
            };

            var params = $.extend(defaultParams, formDataObj, additionalParams);


            $('[name="search"]').val(params.search);
            $('[name="catid"]').val(params.catid);
            $('[name="author"]').val(params.author);
            $('[name="area"]').val(params.area);


            $.post(SickfrontJS.ajaxURL, params, function (data, textStatus, jqXHR) {
                var res = data.data;

                POSTS = res.posts;

                render(res.posts);
                renderResultsCount(res.posts_num);
                renderPagination(res.posts_num, page);
            });
        }

        function _formGetData() {
            var container = $('form[data-form="search-form"]');
            return {
                search: $('[name="search"]', container).val(),
                catid: $('[name="catid"]', container).val(),
                author: $('[name="author"]', container).val(),
                area: $('[name="area"]', container).val()
            };
        }

        function renderResultsCount(resultsCount) {
            $('.search-result-count').html(resultsCount + ' artikler funnet')
        }

        function render(posts) {
            var html = _generatePostsHtml(posts);
            $('.search-result-list').html(html);

            $('[data-draggable-item]').draggable({
                revert: "invalid",
                helper: "clone",
                containment: "document",
                cursor: "move",
                stack: "body",
                zIndex: 9999
            });
        }

        function _generatePostsHtml(posts) {
            var html = '<ul>';
            for (var i = 0; i < posts.length; i++) {
                var post = posts[i];

                html += '<li data-draggable-item="1" data-post-id="' + post.ID + '" data-source="search">' +
                    post.post_title +
                    '<div class="additional-article-info">' +
                        '<div class="pull-right">' + post.post_date + '</div>' +
                        '<div>' + post.post_author_name + '</div>' +
                    '</div>' +
                '</li>';
            }
            html += '</ul>';

            return html;
        }

        function renderPagination(foundPosts, currentPage) {
            currentPage = currentPage || 1;

            var el = $('.sickfront-pagination-wrapper');

            el.empty();
            el.removeData("twbs-pagination");
            el.unbind("page");

            el.twbsPagination({
                totalPages: Math.ceil(foundPosts / LIMIT),
                visiblePages: 4,
                startPage: currentPage,
                prev: '&laquo;',
                next: '&raquo;',
                initiateStartPageClick: false,
                paginationClass: 'pagination pagination-sm',
                onPageClick: function (event, page) {
                    _fetchPosts(page);
                }
            });
        }

        return {
            init: init,
            getPostById: getPostById
        };
    })();


    var DropZone = (function () {
        var STACK = {};

        function _generateMainListHtml(post, container) {
            var setFontSize = container.is('.article-medium');

            return '' +
                '<article data-draggable-article="1" class="on-drag-style" data-post-id="' + post.ID + '" data-source="main">' +
                    '<div class="header">' +
                        '<div class="embed-responsive embed-responsive-16by9">' +
                            '<img class="img-responsive"' +
                                ' src="' + (post.fronted_image ? post.fronted_image.src : (post.post_image) ? post.post_image.src : '') + '"' +
                                (!_isElementOn("image", post) ? ' style="display: none"' : '') +
                            ' />' +
                        '</div>' +
                    '</div>' +
                    '<h4>' +
                        '<a class="title" href="javascript:;" style="' +
                            (!_isElementOn("title", post) ? ' display: none;' : '') +
                            (setFontSize && post.fronted_fontsize ? ' font-size: ' + post.fronted_fontsize + 'px; line-height: ' + (post.fronted_fontsize*1+2) + 'px;': '') +
                        '">' +
                            _nl2br(post.fronted_title || post.post_title) +
                        '</a>' +
                    '</h4>' +
                '</article>';
        }

        function _generateSnappetListHtml(post, container) {
            return '' +
                '<article data-draggable-article="1" class="on-drag-style" data-post-id="' + post.ID + '" data-source="snappet">' +
                    '<div class="title"' + (!_isElementOn("title", post) ? ' style="display: none;"' : '') + '>' + _nl2br(post.post_title) + '</div>' +
                    '<div class="excerpt"' + (!_isElementOn("content", post) ? ' style="display: none;"' : '') + '>' + post.post_content + '</div>' +
                '</article>';
        }

        function init() {
            _loadStack();
            _initDropzoneArea();
            _listenForDropzones();
            _bindSaveCancelButtons();
            _listenToPostParamsChange();
            _bindCloseButtonAction();
            _bindSnappetQuickAdd();

            $(document).on('sickfront.editbox.saveEdit', function(e) {
                $('.dropzone').removeClass('dropzone-selected');
            });
        }

        function _loadStack() {
            var params = {
                action: 'sickfront_get_stack',
                site: SickfrontJS.site,
                _ajax_nonce: SickfrontJS.nonce
            };

            $.post(SickfrontJS.ajaxURL, params, function (data, textStatus, jqXHR) {
                if (data.success && data.data.length != 0) {
                    STACK = data.data;
                    _redraw();
                }
            });
        }

        function _onDrop(e, data) {
            var item = data.draggable;
            var source = item.attr('data-source');
            var postId = item.attr('data-post-id');
            var fromDropZone = item.parent();
            var toDropZone = $(this);
            var toIndexId, toArea, post;

            if (source == 'search') {
                post = Filter.getPostById(postId);
            } else {
                var fromIndexId, fromArea;
                if (fromDropZone.is('[data-dropzone-area]')) {
                    fromArea = fromDropZone.attr('data-dropzone-area');
                    fromIndexId = fromDropZone.attr('data-dropzone-index');
                }

                if (fromArea) {
                    var posts = _removeFromStack(fromArea, fromIndexId);
                    if (posts && posts[0]) {
                        post = posts[0];
                    }
                }
            }

            if (!post) {
                _redraw();
                return;
            }

            toArea = toDropZone.attr('data-dropzone-area');
            toIndexId = toDropZone.attr('data-dropzone-index');

            _addToStack(toArea, toIndexId, post);
            _redraw();
            toDropZone.click();
        }

        function _addToStack(area, indexId, post) {
            if (!STACK[area]) {
                STACK[area] = [];
            }
            if (indexId >= STACK[area].length || !STACK[area][indexId]) {
                STACK[area][indexId] = post;
            }
            else {
                for (var i = 1 + indexId * 1; i < STACK[area].length; i++) {
                    var isIndexEmpty = !STACK[area][i];
                    if (isIndexEmpty) {
                        _removeFromStack(area, i);
                        break;
                    }
                }
                STACK[area].splice(indexId, 0, post);
            }
        }

        function _removeFromStack(area, indexId) {
            return STACK[area].splice(indexId, 1);
        }

        function _updatePostsInStack(post) {
            for (var area in STACK) if (STACK.hasOwnProperty(area)) {
                for (var i = 0; i < STACK[area].length; i++) {
                    var p = STACK[area][i];
                    if (p && p.ID == post.ID) {
                        STACK[area][i] = post;
                    }
                }
            }
        }

        function _bindCloseButtonAction() {
            $('.close-button').click(function () {
                var dropzone = $(this).parent().find('.dropzone');
                var area = dropzone.attr('data-dropzone-area');
                var indexId = dropzone.attr('data-dropzone-index');

                _removeFromStack(area, indexId);
                _redraw();
            });
        }

        function _clearDropzones() {
            var dropzone = $('.dropzone');
            dropzone.empty().removeClass('with-article');
            dropzone.parent().find('.close-button').hide();
        }

        function _redraw() {
            _clearDropzones();

            for (var area in STACK) if (STACK.hasOwnProperty(area)) {
                for (var i = 0; i < STACK[area].length; i++) {
                    var post = STACK[area][i];
                    var el = $('.dropzone[data-dropzone-area=' + area + '][data-dropzone-index=' + i + ']');

                    if (post) {
                        if (area == 'snappet') {
                            el.html(_generateSnappetListHtml(post, el));
                        } else {
                            el.html(_generateMainListHtml(post, el));
                        }

                        el.addClass('with-article');
                        el.parent().find('.close-button').show();
                    }
                    else {
                        el.removeClass('with-article');
                        el.parent().find('.close-button').hide();
                        el.html('');
                    }
                }
            }

            _makeDropzonesDraggable();
        }

        function _bindSnappetQuickAdd() {
            var addBlockEl = $('.snappet-quick-add');
            var showButtonEl = $('.snappet-quick-show', addBlockEl);
            var hideButtonEl = $('.snappet-quick-hide', addBlockEl);
            var formEl = $('.snappet-quick-add-form', addBlockEl);

            var titleEl = $('.title-input', formEl);
            var contentEl = tinyMCE.get('tiny_quickadd_content');

            showButtonEl.click(function(e) {
                e.preventDefault();

                formEl.removeClass('hidden');
                hideButtonEl.removeClass('hidden');
                showButtonEl.addClass('hidden');
            });

            hideButtonEl.click(function(e) {
                e.preventDefault();

                formEl.addClass('hidden');
                showButtonEl.removeClass('hidden');
                hideButtonEl.addClass('hidden');

                titleEl.val('');
                contentEl.setContent('');
            });

            formEl.submit(function(e) {
                e.preventDefault();
                tinyMCE.triggerSave();

                var params = {
                    title: titleEl.val(),
                    content: contentEl.getContent(),
                    action: 'sickfront_add_snappet_post',
                    site: SickfrontJS.site,
                    _ajax_nonce: SickfrontJS.nonce
                };

                $.post(SickfrontJS.ajaxURL, params, function (data, textStatus, jqXHR) {
                    if (data.success) {
                        titleEl.val('');
                        contentEl.setContent('');

                        var post = data.data.post;
                        _addToStack('snappet', 0, post);
                        _redraw();

                        $(document).trigger('sickfront.dropzone.postCreated', post);

                        var el = $('.snappet-block .dropzone:first');
                        _toggleDropzone(el);
                    }

                    hideButtonEl.click();
                });
            });
        }

        function _initDropzoneArea() {
            $('.dropzone').click(function() {
                _toggleDropzone($(this));
            });
        }

        function _toggleDropzone(el) {
            if (!el.is('.with-article')) {
                return;
            }

            var isSelected = el.is('.dropzone-selected');

            $('.dropzone').removeClass('dropzone-selected');

            if (isSelected) {
                $(document).trigger('sickfront.dropzone.unselectPost');
            } else {
                var area = el.attr('data-dropzone-area');
                var index = el.attr('data-dropzone-index');

                el.addClass('dropzone-selected');

                var settings = {
                    editableImage: el.is('[data-editable-image=true]'),
                    editableTitle: true,
                    editableFontSize: el.is('[data-editable-font-size=true]'),
                    editableContent: el.is('[data-editable-content=true]'),
                    area: area
                };

                $(document).trigger('sickfront.dropzone.selectPost', [STACK[area][index], settings]);
            }
        }


        function _listenForDropzones() {
            $('.dropzone[data-dropzone-index]').droppable({
                classes: {
                    "ui-droppable-hover": "ui-state-hover"
                },
                drop: _onDrop
            });
        }

        function _makeDropzonesDraggable() {
            $('[data-draggable-article]').draggable({
                revert: "true",
                helper: "clone",
                containment: "document",
                cursor: "move",
                stack: "body"
            });
        }

        function _bindSaveCancelButtons() {
            $('#revert-btn').click(function () {
                if (confirm("All unsaved changes will be lost. Continue reverting?")) {
                    _loadStack();
                }
            });

            $('#save-btn').click(function () {
                _saveStack();
                $(document).trigger('sickfront.dropzone.savestack');
            });
        }

        function _saveStack() {
            var params = {
                stack: JSON.stringify(STACK),
                action: 'sickfront_save_stack',
                site: SickfrontJS.site,
                _ajax_nonce: SickfrontJS.nonce
            };

            $.post(SickfrontJS.ajaxURL, params, function (data, textStatus, jqXHR) {
                if (data.success) {
                    alert("Saved");
                }
            });
        }

        function _listenToPostParamsChange() {
            $(document).on('sickfront.editbox.postChanged', function (e, post) {
                _updatePostsInStack(post);

                $('.sickfront-dropzone-layout [data-post-id=' + post.ID + ']').each(function () {
                    var el = $(this).parent();
                    var area = el.attr('data-dropzone-area');

                    if (area == 'snappet') {
                        el.html(_generateSnappetListHtml(post, el));
                    } else {
                        el.html(_generateMainListHtml(post, el));
                    }
                });
            });
        }

        return {
            init: init
        };
    }());


    var EditBox = (function (){
        var elElementsTitle, elElementsContent, elElementsImage,
            titleWrapper, titleCheckboxWrapper, titleEl,
            fontSizeWrapper, fontSizeEl,
            contentWrapper, contentCheckboxWrapper, contentEl,
            editLink, previewLink,
            imageWrapper, imageCheckboxWrapper, imageEl, imageUploadBtn, imageRemoveBtn, imagePlaceholder;
        var mainContainer;
        var initPost, editingPost;
        var imageFrame;

        function init() {
            mainContainer = $('.sickfront-live');

            imageCheckboxWrapper = $('.live-image-element', mainContainer);
            imageEl = $('[name=live_fronted_image]', mainContainer);
            imageUploadBtn = $('.live_fronted_image_upload', mainContainer);
            imageRemoveBtn = $('.live_fronted_image_remove', mainContainer);
            imagePlaceholder = $('.live_fronted_image_preview', mainContainer);
            imageWrapper = $('.live-image', mainContainer);

            titleCheckboxWrapper = $('.live-title-element', mainContainer);
            titleWrapper = $('.live-title', mainContainer);
            titleEl = $('[name=live_title]', mainContainer);

            fontSizeWrapper = $('.live-fontsize', mainContainer);
            fontSizeEl = $('.live_fontsize', mainContainer);

            contentCheckboxWrapper = $('.live-content-element', mainContainer);
            contentWrapper = $('.live-content', mainContainer);
            contentEl = $('[name=live_content]', mainContainer);


            elElementsTitle = $(':checkbox', titleCheckboxWrapper);
            elElementsContent = $(':checkbox', contentCheckboxWrapper);
            elElementsImage = $(':checkbox', imageCheckboxWrapper);

            editLink = $('a.edit-link', mainContainer);
            previewLink = $('a.preview-link', mainContainer);

            _initListener();
        }

        function _initListener() {
            fontSizeEl.bootstrapSlider({
                formatter: function(value) {
                    return 'Title Size: ' + value;
                }
            });

            imageUploadBtn.click(function(e) {
                e.preventDefault();

                if (imageFrame) {
                    imageFrame.open();
                    return;
                }

                imageFrame = wp.media({
                    title: 'Upload Image',
                    multiple: false
                });

                imageFrame.on('select', function(e){
                    var uploaded_image = imageFrame.state().get('selection').first();

                    editingPost.fronted_image = {
                        id: uploaded_image.id,
                        src: uploaded_image.toJSON().url
                    };
                    _updateImageArea();
                });
                imageFrame.open();
            });
            imageRemoveBtn.click(function(e) {
                e.preventDefault();

                editingPost.fronted_image = null;
                _updateImageArea();
            });

            $('.cancel-link', mainContainer).click(function(e) {
                e.preventDefault();

                editingPost = initPost;
                sendEvent();

                _hideBlock();
                $(document).trigger('sickfront.editbox.saveEdit');
            });

            $('.close-link', mainContainer).click(function(e) {
                e.preventDefault();
                
                tinyMCE.triggerSave();

                _hideBlock();
                _showAlertBlock();
                $(document).trigger('sickfront.editbox.saveEdit');
            });

            $('.alert-close-button').click(function(e) {
                e.preventDefault();
                _hideAlertBlock();
            });

            $(document).on('sickfront.dropzone.selectPost', function (e, post, settings) {
                initPost = jQuery.extend(true, {}, post);
                editingPost = post;

                _showBlock();
                _loadDataToLiveEdit(settings);
            });

            $(document).on('sickfront.dropzone.unselectPost', function (e) {
                _hideBlock();
            });

            $(document).on('sickfront.dropzone.savestack', function(e){
                _hideAlertBlock();
            });

            _bindLiveTriggers();
        }

        function _showBlock() {
            $('.block-hidden', mainContainer).addClass('hidden');
            $('.block-shown', mainContainer).removeClass('hidden');
        }

        function _hideBlock() {
            $('.block-hidden', mainContainer).removeClass('hidden');
            $('.block-shown', mainContainer).addClass('hidden');
        }

        function _showAlertBlock() {
            var block = $('.alert-block');
            block.removeClass('hidden');
            block.parents('.sickfront-layout-control').addClass('alert-block-shown');
        }

        function _hideAlertBlock() {
            var block = $('.alert-block');
            block.addClass('hidden');
            block.parents('.sickfront-layout-control').removeClass('alert-block-shown');
        }

        function _loadDataToLiveEdit(settings) {
            elElementsImage.prop('checked', _isElementOn('image', editingPost));
            elElementsTitle.prop('checked', _isElementOn('title', editingPost));
            elElementsContent.prop('checked', _isElementOn('content', editingPost));

            if (settings.area == 'snappet') {
                titleEl.val(editingPost.post_title);

                tinyMCE.get('tiny_live_content').setContent(editingPost.post_content);
            } else {
                titleEl.val(editingPost.fronted_title).attr('placeholder', editingPost.post_title);

                // fontSizeEl.bootstrapSlider('enable');
                fontSizeEl.bootstrapSlider('setValue', editingPost.fronted_fontsize * 1 || 64);
            }

            editLink[editingPost.admin_edit_link ? 'show' : 'hide']();
            editLink.attr('href', editingPost.admin_edit_link);

            previewLink[editingPost.guid ? 'show' : 'hide']();
            previewLink.attr('href', editingPost.guid + '&preview=true');


            imageCheckboxWrapper[settings.editableImage ? 'show' : 'hide']();
            imageWrapper[settings.editableImage && _isElementOn('image', editingPost) ? 'show' : 'hide']();

            titleCheckboxWrapper[settings.editableTitle ? 'show' : 'hide']();
            titleWrapper[settings.editableTitle && _isElementOn('title', editingPost) ? 'show' : 'hide']();

            fontSizeWrapper[settings.editableFontSize ? 'show' : 'hide']();

            contentCheckboxWrapper[settings.editableContent ? 'show' : 'hide']();
            contentWrapper[settings.editableContent && _isElementOn('content', editingPost) ? 'show' : 'hide']();


            _updateImageArea(false);
        }

        function _updateImageArea(send_event) {
            var fronted_id = (editingPost.fronted_image && editingPost.fronted_image.id) || '';
            imageEl.val(fronted_id);
            imageRemoveBtn[editingPost.fronted_image ? 'show' : 'hide']();

            var html;
            if (editingPost.fronted_image) {
                html = '<img src="' + editingPost.fronted_image.src + '" class="fronted" />';
            } else if (editingPost.post_image) {
                html = '<img src="' + editingPost.post_image.src + '" class="original" />';
            } else {
                html = 'Post without default image';
            }
            imagePlaceholder.html(html);

            if (typeof send_event == 'undefined' || send_event != false) {
                sendEvent();
            }
        }

        function _bindLiveTriggers() {
            titleEl.bind('input propertychange', function (e) {
                if (editingPost.post_type == 'snappet') {
                    editingPost.post_title = this.value;
                } else {
                    editingPost.fronted_title = this.value;
                }
                sendEvent();
            });

            tinyMCE.get('tiny_live_content').on('keyup', function(e) {
                editingPost.post_content = tinyMCE.get('tiny_live_content').getContent();
                sendEvent();
            });

            fontSizeEl.on('change', function (e) {
                editingPost.fronted_fontsize = e.value.newValue;
                sendEvent();
            });

            elElementsImage.bind('change', function (e) {
                var checked = this.checked;
                imageWrapper[checked ? 'show' : 'hide']();

                _updatePostFrontedElements('image', checked);
                sendEvent();
            });
            elElementsTitle.bind('change', function (e) {
                var checked = this.checked;
                titleWrapper[checked ? 'show' : 'hide']();

                _updatePostFrontedElements('title', checked);
                sendEvent();
            });
            elElementsContent.bind('change', function (e) {
                var checked = this.checked;
                contentWrapper[checked ? 'show' : 'hide']();

                _updatePostFrontedElements('content', checked);
                sendEvent();
            });
        }

        function _updatePostFrontedElements(value, isChecked) {
            if (!$.isArray(editingPost.fronted_elements)) {
                editingPost.fronted_elements = ['image', 'title', 'content'];
            }
            if (isChecked) {
                editingPost.fronted_elements.push(value);
            } else {
                editingPost.fronted_elements = jQuery.grep(editingPost.fronted_elements, function (arrValue) {
                    return arrValue != value;
                });
            }
        }

        function sendEvent() {
            $(document).trigger('sickfront.editbox.postChanged', editingPost)
        }

        return {
            init: init
        };
    }());

    $(document).ready(function () {
        setTimeout(function() {
            Filter.init();
            DropZone.init();
            EditBox.init();
        }, 1);
    });
})(jQuery);
