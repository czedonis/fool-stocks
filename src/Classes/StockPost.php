<?php
namespace Catzedonis\Fmp\Classes;

if (!defined('ABSPATH')) exit;
use WP_Error;
use WP_Post_Type;

if (!class_exists('StockPost'))
{
    class StockPost
    {
        private $stock_name;
        private $article_name;
        public function __construct($stock_name,$article_name)
        {
            $this->stock_name = $stock_name;
            $this->article_name = $article_name;
            $this->register_article_post_type();
            $this->register_recommendation_post_type();
            $this->register_stock_taxonomy();
            $this->setup_template_overrides();

        }
        public function setup_template_overrides()
        {
            add_action('pre_get_posts', [&$this,'archive_front_page'],11);
            add_filter('single_template', [&$this, 'article_template'],11);
            add_filter('archive_template', [&$this, 'archive_article_template'],11);
        }
        public function archive_front_page($wp_query)
        {
            global $post;
            if(is_admin())
            {
                return;
            }
            if($wp_query->get('page_id') == get_option('page_on_front')):

                $wp_query->set('post_type', 'stock_article');
                $wp_query->set('page_id', ''); //Empty

                //Set properties that describe the page to reflect that
                //we aren't really displaying a static page
                $wp_query->is_page = 0;
                $wp_query->is_singular = 0;
                $wp_query->is_archive = 1;
                $wp_query->is_archive = 1;

            endif;
        }
        public function article_template($single): string
        {
            global $post;

            /* Checks for single template by post type */
            if ( $post->post_type == 'stock_article' && locate_template( array( 'src/templates/single-stock_article.php' ) ) !== $single) {
                return PLUGIN_DIR . 'src/templates/single-stock_article.php';
            }
            if ( $post->post_type == 'stock_recommendation' && locate_template( array( 'src/templates/single-stock_recommendation.php' ) ) !== $single) {
                return PLUGIN_DIR . 'src/templates/single-stock_recommendation.php';
            }

            return $single;
        }
        public function archive_article_template($page): string
        {
            global $post;

            /* Checks for single template by post type */
            if ( $post->post_type == 'stock_article' && locate_template( array( 'src/templates/archive-stock_article.php' ) ) !== $page) {
                return PLUGIN_DIR . 'src/templates/archive-stock_article.php';
            }
            elseif ( $post->post_type == 'stock_recommendation' && locate_template( array( 'src/templates/archive-stock_recommendation.php' ) ) !== $page) {
                return PLUGIN_DIR . 'src/templates/archive-stock_recommendation.php';
            }

            return $page;
        }
        public function register_stock_taxonomy()
        {
            $labels = array(
                'name' => _x( 'Ticker Symbol', 'taxonomy general name' ),
                'singular_name' => _x( 'Ticker Symbol', 'taxonomy singular name' ),
                'search_items' =>  __( 'Search Ticker Symbols' ),
                'all_items' => __( 'All Ticker Symbols' ),
                'edit_item' => __( 'Edit Ticker Symbol' ),
                'update_item' => __( 'Update Ticker Symbol' ),
                'add_new_item' => __( 'Add New Ticker Symbol' ),
                'new_item_name' => __( 'New Ticker Symbol' ),
                'menu_name' => __( 'Ticker Symbol' )
            );

            register_taxonomy('stocks',array($this->stock_name,$this->article_name,'page'), array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'stock' ),
            ));
        }
        /**
         * registers News Articles post type
         * @return WP_Error|WP_Post_Type
         */
        public function register_recommendation_post_type()
        {

            $labels = array(
                'name' => _x('Stock Recommendation', 'Post Type General Name'),
                'singular_name' => _x('Stock Recommendation', 'Post Type Singular Name'),
                'menu_name' => __('Stock Recommendations'),
                'name_admin_bar' => __('Stock Recommendation'),
                'all_items' => __('All Stock Recommendations'),
                'add_new_item' => __('Add New Stock Recommendation'),
                'add_new' => __('Add Stock Recommendation'),
                'new_item' => __('New Stock Recommendation'),
                'edit_item' => __('Edit Stock Recommendation'),
                'update_item' => __('Update Stock Recommendation'),
                'view_item' => __('View Stock Recommendation'),
                'search_items' => __('Search Stock Recommendation'),
                'not_found' => __('Stock Recommendation not found'),
                'not_found_in_trash' => __('Stock Recommendation Not found in Trash'),
            );

            $rewrite = array(
                'slug' => $this->stock_name,
                'with_front' => false,
                'pages' => true,
                'feeds' => false,
            );

            $args = array(
                'label' => __($this->stock_name),
                'description' => __('Displays Stock Recommendation Information'),
                'labels' => $labels,
                'supports' => array('title','editor','author','thumbnail','custom_fields','excerpt'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 21,
                'menu_icon' => 'dashicons-megaphone',
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => false,
                'has_archive' => true,
                'exclude_from_search' => true,
                'rewrite' => $rewrite,
                'publicly_queryable' => true,
                'capability_type' => 'post'
            );
            register_post_type($this->stock_name, $args);

        }
        /**
         * registers News Articles post type
         * @return WP_Error|WP_Post_Type
         */
        public function register_article_post_type()
        {

            $labels = array(
                'name' => _x('News Article', 'Post Type General Name'),
                'singular_name' => _x('News Article', 'Post Type Singular Name'),
                'menu_name' => __('News Article'),
                'name_admin_bar' => __('News Article'),
                'all_items' => __('All News Article'),
                'add_new_item' => __('Add New News Article'),
                'add_new' => __('Add News Article'),
                'new_item' => __('New News Article'),
                'edit_item' => __('Edit News Article'),
                'update_item' => __('Update News Article'),
                'view_item' => __('View News Article'),
                'search_items' => __('Search News Article'),
                'not_found' => __('News Article not available'),
                'not_found_in_trash' => __('News Article Not found in Trash'),
            );

            $rewrite = array(
                'slug' => $this->article_name,
                'with_front' => false,
                'pages' => true,
                'feeds' => false,
            );

            $args = array(
                'label' => __($this->article_name),
                'description' => __('Displays News Article Information'),
                'labels' => $labels,
                'supports' => array('title','editor','author','thumbnail','custom_fields','excerpt'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 20,
                'menu_icon' => 'dashicons-media-text',
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => false,
                'has_archive' => true,
                'exclude_from_search' => false,
                'rewrite' => $rewrite,
                'publicly_queryable' => true,
                'capability_type' => 'post'
            );

            register_post_type($this->article_name, $args);
        }
        public function deactivate()
        {
            unregister_post_type( $this->stock_name );
            unregister_post_type( $this->article_name );
        }
    }
}