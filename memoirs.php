<?php
/**
* Plugin Name: Memoirs
* Plugin URI: https://github.com/rmcfadden/memoirs
* Description: A brief description about your plugin.
* Version: 1.0
* Author: Ryan McFadden
* Author URI: https://github.com/rmcfadden
* License: GPLv2
*/

add_action('plugins_loaded', array( 'memoirs', 'init' ));
register_activation_hook(__FILE__, array('memoirs',  'activation' ));


class memoirs {

    private $options;
    private static $page_name = 'memoirs';
    private static $options_name = 'memoirs';

    public static function init() {
        $class = __CLASS__;
        new $class;

    }

    public static function activation() {
        $new_options = array(
        );

	    if ( get_option(memoirs::$options_name ) !== false ) {
      	    update_option(memoirs::$options_name, $new_options );
        } 
        else{
   		    add_option(memoirs::$options_name, $new_options );
        }

    }

    public function __construct() { 
        add_action( 'init', array($this, "create_post_type") );
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'save_post', array($this,'add_fields'),10,2);
        add_filter( 'template_include', array($this,'include_template_function'),1);
        add_action('add_meta_boxes', array($this,'add_custom_meta_boxes'));
    }

    public  function create_post_type() {
        register_post_type( 'memoirs',
            array(
                'labels' => array(
                    'name' => 'Memoirs',
                    'singular_name' => 'Memoir',
                    'add_new' => 'Add new',
                    'add_new_item' => 'Add new Memoir',
                    'edit' => 'Edit',
                    'edit_item' => 'Edit Memoir',
                    'new_item' => 'New Memoir',
                    'view' => 'View',
                    'view_item' => 'View Memoir',
                    'search_items' => 'Search memoirs',
                    'not_found' => 'No memoirs found',
                    'not_found_in_trash' => 'No Memoirs found in Trash',
                    'parent' => 'Parent Memoirs Review'
                ),
 
                'public' => true,
                'menu_position' => 15,
                'supports' => array( 'thumbnail'),
                'taxonomies' => array( '' ),
                'has_archive' => false
            )
        );
    }


    public function add_fields( $memoir_id, $memoir ) {
        /*
        if ( $movie_review->post_type == 'movie_reviews' ) {
            // Store data in post meta table if present in post data
            if ( isset( $_POST['movie_review_director_name'] ) && $_POST['movie_review_director_name'] != '' ) {
                update_post_meta( $movie_review_id, 'movie_director', $_POST['movie_review_director_name'] );
            }
            if ( isset( $_POST['movie_review_rating'] ) && $_POST['movie_review_rating'] != '' ) {
                update_post_meta( $movie_review_id, 'movie_rating', $_POST['movie_review_rating'] );
            }
        }*/
    }


    function include_template_function( $template_path ) {
        if ( get_post_type() == 'memoirs' ) {
            if ( is_single() ) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ( $theme_file = locate_template( array ( 'single-memoirs.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path( __FILE__ ) . '/single-memoirs.php';
                }
            }
        }
        return $template_path;
    }


    public function add_custom_meta_boxes()
    {
        add_meta_box('add_memoir_meta_box',
            'Memoirs Details',
            array($this,'add_memoir_meta_box'),
            'posts'
        );         
    }

    public function admin_init()
    {
        add_meta_box('add_memoir_meta_box',
            'Memoirs Details',
            array($this,'add_memoir_meta_box'),
            'memoirs'
        );   
    }


    function add_memoir_meta_box( $memoir ) {
        // Retrieve current name of the Director and Movie Rating based on review ID
        $movie_director = esc_html( get_post_meta( $memoir->ID, 'movie_director', true ) );
        $movie_rating = intval( get_post_meta( $memoir->ID, 'movie_rating', true ) );

        ?>
        <table>
            <tr>
                <td style="width: 100%">Memoir</td>
                <td><input type="text" size="80" name="memoir_name" value="<?php echo $movie_director; ?>" /></td>
            </tr>
            <tr>
                <td style="width: 150px">Movie Rating</td>
                <td>
                    <select style="width: 100px" name="movie_review_rating">
                    <?php
                    // Generate all items of drop-down list
                    for ( $rating = 5; $rating >= 1; $rating -- ) {
                    ?>
                        <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $movie_rating ); ?>>
                        <?php echo $rating; ?> stars <?php } ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

}