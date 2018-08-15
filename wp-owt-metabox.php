<?php
/*
  Plugin Name: WP OWT Metabox
  Description: This is a simple plugin for purpose of learning about wordpress metaboxes
  Version: 1.0.0
  Author: Online Web Tutor
 */

function wpl_owt_custom_init_cpt() {
    $args = array(
        'public' => true,
        'label' => 'Books',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );
    register_post_type('book', $args);
}

add_action('init', 'wpl_owt_custom_init_cpt');

function wpl_owt_register_metabox() {

    //page
    add_meta_box("owt-page-id", "OWT Page Metabox", "wpl_owt_pages_function", "page", "normal", "high");

    //post
    add_meta_box("owt-post-id", "OWT Post Metabox", "wpl_owt_post_function", "post", "side", "high");
}

add_action("add_meta_boxes", "wpl_owt_register_metabox");

function wpl_owt_register_metabox_cpt() {

    //custom post type  
    add_meta_box("owt-cpt-id", "OWT Book Metabox", "wpl_owt_book_function", "book", "side", "high");
}

add_action("add_meta_boxes_book", "wpl_owt_register_metabox_cpt");

//add_action("wp_dashboard_setup", "wpl_owt_register_metabox_dashboard");

function wpl_owt_register_metabox_dashboard() {

    //add_meta_box("owt-dasbhoard-id", "OWT Dashboard Metabox", "wpl_owt_dashboard_function", "dashboard", "side", "high");
    remove_meta_box("dashboard_quick_press", "dashboard", "side");

    remove_meta_box("dashboard_activity", "dashboard", "side");
}

function remove_dashboard_widgets() {
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
    remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News
    // use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

// callback function for metabox at custom post type 'book'
function wpl_owt_book_function($post) {
    wp_nonce_field(basename(__FILE__), "wp_owt_cpt_nonce");
    ?>
    <!---p>This is custom owt metabox for custom post type</p>
    <p><a href="https://github.com/owthub/wp-owt-metabox" target="_blank">Github link</a></p-->
    <div>
        <label for="txtPublisherName">Publisher name</label>
        <?php
        $publisher_name = get_post_meta($post->ID, "book_publisher_name", true);
        ?>
        <input type="text" name="txtPublisherName" value="<?php echo $publisher_name; ?>" placeholder="Publisher name"/>
    </div>
    <?php
}

add_action("save_post", "wpl_owt_save_metabox_data", 10, 2);

function wpl_owt_save_metabox_data($post_id, $post) {

    // we have verfied the nonce
    if (!isset($_POST['wp_owt_cpt_nonce']) || !wp_verify_nonce($_POST['wp_owt_cpt_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // verifying slug value
    $post_slug = "book";
    if ($post_slug != $post->post_type) {
        return;
    }

    //save value to db field
    $pub_name = '';
    if (isset($_POST['txtPublisherName'])) {
        $pub_name = sanitize_text_field($_POST['txtPublisherName']);
    } else {
        $pub_name = '';
    }
    update_post_meta($post_id, "book_publisher_name", $pub_name);
}

function wpl_owt_authors_metabox() {

    //register custom meta box
    add_meta_box("owt-author-id", "OWT Author Metabox", "wpl_owt_author_callback_function", "book", "side", "high");
}

add_action("add_meta_boxes", "wpl_owt_authors_metabox");

function wpl_owt_author_callback_function($post) {

    wp_nonce_field(basename(__FILE__), "wpl_owt_author_nonce");
    ?>
    <p>
        <label for="ddauthor">Select Author</label>
        <select name="ddauthor">
            <?php
            $post_id = $post->ID;

            $author_id = get_post_meta($post_id, "owt_book_author_name", true);

            $all_authors = get_users(array("role" => "author"));
            foreach ($all_authors as $index => $author) {

                $selected = "";
                if ($author_id == $author->data->ID) {
                    $selected = 'selected="selected"';
                }
                ?>
                <option value="<?php echo $author->data->ID; ?>" <?php echo $selected; ?>><?php echo $author->data->display_name ?></option>
                <?php
            }
            ?>

        </select>
    </p>
    <?php
}

add_action("save_post", "wpl_owt_save_author", 10, 2);

function wpl_owt_save_author($post_id, $post) {

    //nonce value first step verification
    if (!isset($_POST['wpl_owt_author_nonce']) || !wp_verify_nonce($_POST['wpl_owt_author_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    //verifying post slug
    $book_slug = "book";
    if ($book_slug != $post->post_type) {
        return $post_id;
    }

    $author_name = "";

    if (isset($_POST['ddauthor'])) {
        $author_name = sanitize_text_field($_POST['ddauthor']);
    } else {
        $author_name = '';
    }

    update_post_meta($post_id, "owt_book_author_name", $author_name);
}
