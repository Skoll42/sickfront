<?php

class Sickfront_AdminAjaxManager
{
    public function __construct()
    {
        add_action('wp_ajax_sickfront_filter_posts', array($this, 'filterPosts'));
        add_action('wp_ajax_sickfront_get_stack', array($this, 'getStack'));
        add_action('wp_ajax_sickfront_save_stack', array($this, 'saveStack'));
        add_action('wp_ajax_sickfront_add_snappet_post', array($this, 'addSnappetPost'));
    }

    function filterPosts()
    {
        $this->initAjaxRequest();

        $catid = !empty($_POST['catid']) ? $_POST['catid'] : false;
        $search = !empty($_POST['search']) ? $_POST['search'] : false;
        $author = !empty($_POST['author']) ? $_POST['author'] : false;
        $offset = !empty($_POST['offset']) && is_numeric($_POST['offset']) ? (int)$_POST['offset'] : 0;
        $post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : false;
        $limit = !empty($_POST['limit']) && is_numeric($_POST['limit']) ? (int)$_POST['limit'] : 10;

        $args = array();

        if ( $catid ) {
            $args['cat'] = $catid;
        }
        if ( $author ) {
            $args['author'] = $author;
        }

        $args['s'] = '';
        if ( $search ) {
            if ( is_numeric($search) ) {
                $args['p'] = $search;
            } else {
                $args['s'] = $search;
            }
        }

        if($post_type) {
            $args['post_type'] = $post_type;
        } else {
            $args['post_type'] = 'post';
        }

        $args['posts_per_page'] = $limit;

        if ( $offset ) {
            $args['offset'] = $offset;
        }

        $args['post_status'] = array('publish', 'future');
        $args['fields'] = 'ids';

        $query = new WP_Query($args);

        $res = [];
        foreach ($query->posts as $post_id) {
            $res[] = $this->getPost($post_id);
        }


        wp_send_json_success(array(
            'posts' => $res,
            'posts_num' => $query->found_posts,
        ));
    }

    function getStack()
    {
        $this->initAjaxRequest();
        $site = isset($_POST['site']) ? $_POST['site'] : false;

        if( $site === false ) {
            wp_send_json_error(array('message' => 'Json error: '. json_last_error()));
        }

        $list = Sickfront_DbHelper::get_stack(null, $site);

        $stack = [];
        foreach ($list as $area => $area_data) {
            $stack[$area] = [];
            foreach ($area_data as $index => $post_id) {
                $stack[$area][$index] = $this->getPost($post_id);
            }
        }

        wp_send_json_success($stack);
    }

    function saveStack()
    {
        $this->initAjaxRequest();

        $site = isset($_POST['site']) ? $_POST['site'] : false;
        $category = get_category_by_slug($site);
        if ( !$category ) {
            wp_send_json_error(array('message' => 'Wrong site name.'));
        }

        if( !isset($_POST['stack']) || empty($_POST['stack']) ) {
            wp_send_json_error(array('message' => 'Empty stack.'));
        }

        $stack = json_decode(stripslashes($_POST['stack']), true);
        if( $stack === null ) {
            wp_send_json_error(array('message' => 'Json error: '. json_last_error()));
        }

        $data = [];
        foreach ($stack as $area => $area_data) {
            $data[$area] = [];
            foreach ($area_data as $index => $post) {
                if ($area == 'snappet') {
                    wp_update_post([
                        'ID' => $post['ID'],
                        'post_title' => $post['post_title'],
                        'post_content' => $post['post_content'],
                    ]);

                    update_post_meta($post['ID'], '_snappet_fronted_elements', 'field_58ac29e2d9c91');
                    update_post_meta($post['ID'], 'snappet_fronted_elements', $post['fronted_elements']);
                } else {
                    update_post_meta($post['ID'], '_article_fronted_elements', 'field_5876b3fc1c103');
                    update_post_meta($post['ID'], 'article_fronted_elements', $post['fronted_elements']);

                    update_post_meta($post['ID'], '_article_fronted_title', 'field_5876b39d1c101');
                    update_post_meta($post['ID'], 'article_fronted_title', $post['fronted_title']);

                    update_post_meta($post['ID'], '_article_fronted_fontsize', 'field_5876be215eeae');
                    update_post_meta($post['ID'], 'article_fronted_fontsize', $post['fronted_fontsize']);

                    $fronted_image_id = isset($post['fronted_image'], $post['fronted_image']['id']) ? $post['fronted_image']['id'] : '';
                    update_post_meta($post['ID'], '_article_fronted_image', 'field_5876b490ed127');
                    update_post_meta($post['ID'], 'article_fronted_image', $fronted_image_id);
                }

                $data[$area][$index] = $post['ID'];
            }
        }

        Sickfront_DbHelper::save_stack($data, $category->slug);

        do_action('sickfront_save_stack', $stack, $site);

        wp_send_json_success();
    }

    function addSnappetPost()
    {
        $this->initAjaxRequest();

        if( !isset($_POST['title']) || empty($_POST['title']) ) {
            wp_send_json_error(array('message' => 'Empty title.'));
        }

        $site = isset($_POST['site']) ? $_POST['site'] : false;
        $category = get_category_by_slug($site);
        if ( !$category ) {
            wp_send_json_error(array('message' => 'Wrong site name.'));
        }

        $postID = wp_insert_post([
            'post_title' => $_POST['title'],
            'post_content' => $_POST['content'],
            'post_type' => 'snappet',
            'post_category' => [$category->slug],
            'post_status' => 'publish',
        ]);

        if (is_wp_error($postID)) {
            // TODO: add error
            wp_send_json_error();
        }

        wp_send_json_success([
            'post' => $this->getPost($postID),
        ]);
    }

    private function initAjaxRequest()
    {
        @header('X-Sickfront-Version: 0.1');

        if( !check_ajax_referer('sickfront-nonce') ) {
            wp_send_json_error(array('message' => 'incorrect nonce'));
        } elseif( !is_user_logged_in() ) {
            wp_send_json_error(array('message' => 'not logged in'));
        }
    }

    private function getPost($post_id)
    {
        $post = get_post($post_id);
        if (!$post) {
            return null;
        }

        $data = [
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'post_author_name' => get_userdata($post->post_author)->display_name,

            'post_date' => $post->post_date,
            'post_type' => $post->post_type,
            'admin_edit_link' => get_edit_post_link($post->ID, '&'),
        ];

        if ($post->post_type == 'snappet') {
            $content = $post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);

            $data = array_merge([
                'fronted_elements' => bt_get_snappet_fronted_elements($post->ID),
                'post_content' => $content,
            ], $data);
        } else {
            $data = array_merge([
                'post_image' => $this->getDefaultImageInfo($post->ID),
                'guid' => $post->guid,

                'fronted_elements' => bt_get_article_fronted_elements($post->ID),
                'fronted_fontsize' => bt_get_article_fronted_fontsize($post->ID),
                'fronted_title' => bt_get_real_article_fronted_title($post->ID),
                'fronted_image' => $this->getRealFrontedImageInfo($post->ID),
            ], $data);
        }

        return $data;
    }

    private function getDefaultImageInfo($post_id)
    {
        $image_id = bt_get_article_header_image_id($post_id);
        return $this->getImageInfo($image_id);
    }

    private function getRealFrontedImageInfo($post_id)
    {
        $image_id = bt_get_real_article_fronted_image($post_id);
        return $this->getImageInfo($image_id);
    }

    private function getImageInfo($image_id, $size = 'bt-medium')
    {
        if (!$image_id) {
            return null;
        }

        $image = wp_get_attachment_image_src($image_id, $size, false);
        if (!$image) {
            return null;
        }

        list($src, $width, $height) = $image;
        return [
            'id' => $image_id,
            'src' => $src,
        ];
    }
}
