<?php
//
///**
// * @package StockInfo
// * @version 1.1.1
// */
///*
//Plugin Name: Stock Recommendations and News
//Description: Add stock recommendations, News Articles and Stock Company information to any site
//logged in
//Author: Christine 'Cat' Zedonis
//Version: 1.1.0
//
//*/
namespace Catzedonis\Fmp;
use Catzedonis\Fmp\Classes\CompanyPageTemplate;
use Catzedonis\Fmp\Classes\StockAPIDatabase;
use Catzedonis\Fmp\Classes\StockPost;

require 'vendor/autoload.php';

if (!defined('ABSPATH')) exit;

if (!class_exists('StockInfo'))
{
    /**
     * Class StockInfo
     */
    class StockInfo
    {

        private $stock_name = 'stock_recommendation';
        private $article_name = 'stock_article';
        protected $file_base;
        private $table_name;
        private $PostTypes;
        private $DatabaseAPI;


        /**
         * StockInfo constructor.
         */
        public function __construct()
        {
            global $wpdb;
            define( 'PLUGIN_DIR', dirname(__FILE__).'/' );
            add_action( 'init', array( &$this, 'load_css_js' ), 15 );
            $this->file_base = plugin_dir_path(dirname(__FILE__)) . 'fmp/stock-info.php';
            $this->table_name = $wpdb->prefix . 'stock_info';
            $this->DatabaseAPI = new StockAPIDatabase($this->table_name);
            add_action('init',[&$this, 'create_post_types']);
            register_activation_hook(PLUGIN_DIR, [&$this, 'activate_plugin']);
            register_deactivation_hook(PLUGIN_DIR, [&$this, 'deactivate_plugin']);
            if(is_admin())
            {
                add_action( 'admin_init', [&$this,'register_settings'] );
                add_action( 'admin_menu', [&$this,'add_settings_page'] );
                add_action( 'wp_ajax_my_action', [$this->DatabaseAPI,'update_stock_db'] );
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), [&$this, 'add_plugin_settings_link']);
            }
            new CompanyPageTemplate();



        }
        public function register_settings()
        {
            register_setting( 'stock_info_options_group', 'stock_info_last_update' );
            register_setting( 'stock_info_options_group', 'stock_info_update_frequency' );
        }

        public function add_settings_page()
        {
            add_submenu_page('edit.php?post_type=stock_recommendation',
                'Update Stock Info from API',
                'Refresh Stock Info',
                'administrator',
                'update_stock_info',
                [&$this,'fmp_options_page_html'],
                20
            );
        }
        /**
         * adds direct settings link to plugins page when active
         * @param $links
         * @return mixed
         */
        public function add_plugin_settings_link($links)
        {
            $settings_link = '<a href="edit.php?post_type=stock_recommendation&page=update_stock_info">Refresh Stock Info</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
        public function fmp_options_page_html()
        {
            $updated = $this->DatabaseAPI->update_stock_db();
            ?>
            <div>
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <?php settings_fields( 'stock_info_options_group' ); ?>
                <h3><?php echo $updated; ?></h3>
                <?php do_settings_sections( 'stock_info_options_group' ); ?>
                <table>
                    <tr valign="top">
                        <th scope="row"><label for="stock_info_last_update">Last Stock Info Update Time:</label></th>
                        <td><input type="text" id="stock_info_last_update" name="stock_info_last_update" value="<?php echo get_option('stock_info_last_update'); ?>" readonly aria-readonly/></td>
                    </tr>
                </table>
            </div>
            <?php
        }

        /**
         * flush rewrite rules on activation
         */
        public function activate_plugin()
        {
            new StockAPIDatabase($this->table_name);
        }

        /**
         * create custom post type to hold our advice, only uses Title and Excerpt
         */
        public function create_post_types()
        {
            $this->PostTypes = new StockPost($this->stock_name,$this->article_name);
            flush_rewrite_rules();
        }

        /**
         * load js and necessary dependency and css files
         */
        public function load_css_js()
        {
            wp_enqueue_style( 'bootstrapcss','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', false, null );
            wp_register_style( 'fmp_css',  '/wp-content/plugins/fmp/src/includes/css/fmp.css', false, NULL,'ALL' );
            wp_enqueue_style('fmp_css');
        }

        /**
         *  deactivation of plugin, flushes rewrite rules
         * and dequeues unneeded scripts
         */
        public function deactivate_plugin()
        {

            $this->DatabaseAPI->deactivate();
            $this->PostTypes->deactivate();
            flush_rewrite_rules();
        }

    }
}
new StockInfo();