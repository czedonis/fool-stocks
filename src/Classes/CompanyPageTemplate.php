<?php
namespace Catzedonis\Fmp\Classes;

if (!class_exists('CompanyPageTemplate')) {
    class CompanyPageTemplate
    {

        protected $templates;

        public function __construct()
        {

            $this->templates = array();

            if (version_compare(floatval(get_bloginfo('version')), '4.7', '<'))
            {

                add_filter('page_attributes_dropdown_pages_args', array($this, 'register_project_templates'));

            } else
            {
                add_filter('theme_page_templates', array($this, 'add_new_template'));
            }

            add_filter('wp_insert_post_data', array($this, 'register_project_templates'));

            add_filter('template_include', array($this, 'view_project_template'));

            $this->templates = array('../templates/page-company-details.php' => 'Company Details',);

        }

        public function register_project_templates($atts)
        {

            $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

            $templates = wp_get_theme()->get_page_templates();
            if (empty($templates)) {
                $templates = array();
            }

            wp_cache_delete($cache_key, 'themes');

            $templates = array_merge($templates, $this->templates);

            wp_cache_add($cache_key, $templates, 'themes', 1800);

            return $atts;

        }

        public function add_new_template($posts_templates)
        {
            $posts_templates = array_merge($posts_templates, $this->templates);
            return $posts_templates;
        }

        public function view_project_template($template)
        {

            // Get global post
            global $post;

            // Return template if post is empty
            if (!$post)
            {
                return $template;
            }

            // Return default template if we don't have a custom one defined
            if (!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)]))
            {
                return $template;
            }

            $file = plugin_dir_path(__FILE__) . get_post_meta($post->ID, '_wp_page_template', true);

            // Just to be safe, we check if the file exist first
            if (file_exists($file)) {
                return $file;
            } else {
                echo $file;
            }

            // Return template
            return $template;

        }
    }
}