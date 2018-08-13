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
function wpl_owt_book_function() {
    wp_nonce_field(basename(__FILE__), "wp_owt_cpt_nonce");
    ?>
    <!---p>This is custom owt metabox for custom post type</p>
    <p><a href="https://github.com/owthub/wp-owt-metabox" target="_blank">Github link</a></p-->
    <div>
        <label for="txtPublisherName">Publisher name</label>
        <input type="text" name="txtPublisherName" placeholder="Publisher name"/>
    </div>
    <?php
}
