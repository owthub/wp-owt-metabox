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

/**
 * Generated by the WordPress Meta Box generator
 * at http://jeremyhixon.com/tool/wordpress-meta-box-generator/
 */

function owt_online_metabox_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function owt_online_metabox_add_meta_box() {
	add_meta_box(
		'owt_online_metabox-owt-online-metabox',
		__( 'OWT Online Metabox', 'owt_online_metabox' ),
		'owt_online_metabox_html',
		'book',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'owt_online_metabox_add_meta_box' );

function owt_online_metabox_html( $post) {
	wp_nonce_field( '_owt_online_metabox_nonce', 'owt_online_metabox_nonce' ); ?>

	<p>This is sample metabox that we have generated by Online Tool</p>

	<p>
		<label for="owt_online_metabox_enter_name"><?php _e( 'Enter Name', 'owt_online_metabox' ); ?></label><br>
		<input type="text" name="owt_online_metabox_enter_name" id="owt_online_metabox_enter_name" value="<?php echo owt_online_metabox_get_meta( 'owt_online_metabox_enter_name' ); ?>">
	</p><?php
}

function owt_online_metabox_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['owt_online_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['owt_online_metabox_nonce'], '_owt_online_metabox_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['owt_online_metabox_enter_name'] ) )
		update_post_meta( $post_id, 'owt_online_metabox_enter_name', esc_attr( $_POST['owt_online_metabox_enter_name'] ) );
}
add_action( 'save_post', 'owt_online_metabox_save' );

/*
	Usage: owt_online_metabox_get_meta( 'owt_online_metabox_enter_name' )
*/

class owtsamplemetaboxMetabox {
	private $screen = array(
		'post',
	);
	private $meta_fields = array(
		array(
			'label' => 'Enter Some Value',
			'id' => 'entersomevalue_49412',
			'type' => 'text',
		),
	);
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}
	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'owtsamplemetabox',
				__( 'OWT Sample Metabox', 'textdomain' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'normal',
				'default'
			);
		}
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'owtsamplemetabox_data', 'owtsamplemetabox_nonce' );
		echo 'This is sample metabox code';
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = $meta_field['default']; }
			switch ( $meta_field['type'] ) {
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value
					);
			}
			$output .= $this->format_rows( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}
	public function format_rows( $label, $input ) {
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['owtsamplemetabox_nonce'] ) )
			return $post_id;
		$nonce = $_POST['owtsamplemetabox_nonce'];
		if ( !wp_verify_nonce( $nonce, 'owtsamplemetabox_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		foreach ( $this->meta_fields as $meta_field ) {
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
		}
	}
}
if (class_exists('owtsamplemetaboxMetabox')) {
	new owtsamplemetaboxMetabox;
};