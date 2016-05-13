<?php
/**
 * Import / Export module
 * http://www.aa-team.com
 * ======================
 *
 * @package		kingdom
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
!defined('ABSPATH') and exit;
if (class_exists('BKM_export') != true) {
    class BKM_export
    {
        
        const WXR_VERSION = '1.0';
        
        /**
         * parent storage
         *
         * @var array
         */
        public $parent = array();
        public $config = array();
        public $search_in_post_meta = array();
        
        /**
         * The constructor
         */
        public function __construct($parent = array())
        {
            // load parent
            $this->parent = $parent;
            
            // load the search in post mets 
            $this->read_post_metas();
            
            // create AJAX request
            add_action('wp_ajax_BKM_export_content', array(
                $this,
                'export'
            ));
        }
        
        public function read_post_metas()
        {
            ob_start();
            if (is_file($this->parent->path('EXPORT_DIR', 'options.php'))) {
                require_once $this->parent->path('EXPORT_DIR', 'options.php');
            }
            $settings = ob_get_clean();
            
            if (trim($settings) != "") {
                $this->search_in_post_meta = @json_decode($settings, true);
            }
        }
        
        public function print_interface()
        {
            $html = array();
            
            $html[] = '
				<div id="BKM_iw-section-content">
					<form action="" method="get" id="export-iw-export">
						<h3>Choose what to export</h3>
						<p><label><input name="theme-options" checked="checked" type="checkbox">All Theme Options</label></p>
						<p class="description">This will contain all your theme options.</p>
						
						<p><label><input name="sidebars" type="checkbox">All Sidebars</label></p>
						<p class="description">This will contain all your sidebars.</p>
						
						<p><label><input name="widgets" type="checkbox">All Widgets</label></p>
						<p class="description">This will contain all your widgets.</p>
						
						<p><label><input name="all" checked="checked" type="checkbox"> All content</label></p>
						<p class="description">This will contain all of your posts, pages, comments, custom fields, terms, navigation menus and custom posts.</p>';
            
            $html[]             = '<div id="iw-export-post-types">';
            $custom_post_types  = get_post_types(array(
                '_builtin' => false,
                'can_export' => true
            ), 'objects');
            $default_post_types = get_post_types(array(
                '_builtin' => true,
                'can_export' => true
            ), 'objects');
            
            $post_types = array();
            foreach ($custom_post_types as $key => $value) {
                $post_types[esc_attr($value->name)] = esc_html($value->label);
            }
            foreach ($default_post_types as $key => $value) {
                if ($value->name == 'attachment')
                    continue;
                $post_types[esc_attr($value->name)] = esc_html($value->label);
            }
            asort($post_types);
            
            foreach ($post_types as $key => $value) {
                $html[] = '<p><label><input type="checkbox" checked="checked" name="' . ($key) . '" /> ' . (($value)) . '</label></p>';
            }
            $html[] = '</div>';
            $html[] = '
					    <p class="submit"><input type="submit" id="submit" class="button-secondary" value="&nbsp Export Content &nbsp"></p>
					</form>
				</div>';
            
            return implode("\n", $html);
        }
        
        public function options_export()
        {
            global $kingdom;
            
            $theme          = $kingdom;
            $attachments    = array();
            $options        = array();
            $ignore_modules = array(
                'setup_backup'
            );
            if (isset($theme->cfg['modules']) && count($theme->cfg['modules']) > 0) {
                foreach ($theme->cfg['modules'] as $module_id => $module) {
                    $GLOBALS['kingdom']                      = $theme;
                    $options['kingdom_module_' . $module_id] = $module['status'];
                    if (is_file($module['folder_path'] . 'options.php') === false || in_array($module_id, $ignore_modules)) {
                        continue;
                    }
                    
                    $module_options = array();
                    ob_start();
                    require_once($module['folder_path'] . 'options.php');
                    $json = json_decode(ob_get_contents(), true);
                    ob_clean();
                    if (is_array($json) && count($json) > 0) {
                        foreach ($json as $boxs) {
                            foreach ($boxs as $box_id => $box) {
                                // get the values from DB
                                $settings = get_option('kingdom_' . $box_id);
                                if (count($box['elements']) > 0) {
                                    // loop the box elements now
                                    foreach ($box['elements'] as $elm_id => $value) {
                                        
                                        // some helpers. Reset an each loop, prevent collision
                                        $val          = '';
                                        $select_value = '';
                                        $checked      = '';
                                        $option_name  = isset($option_name) ? $option_name : '';
                                        
                                        // Set default value to $val
                                        if (isset($value['std'])) {
                                            $val = $value['std'];
                                        }
                                        
                                        // If the option is already saved, ovveride $val
                                        if (($value['type'] != 'info')) {
                                            if (isset($settings[($elm_id)])) {
                                                $val = $settings[($elm_id)];
                                                
                                                // Striping slashes of non-array options
                                                if (!is_array($val)) {
                                                    $val = stripslashes($val);
                                                }
                                                if ($value['type'] == 'upload_image_wp' && (int) $val > 0) {
                                                    $attachments[] = $val;
                                                }
                                                $options['kingdom_' . $box_id][$elm_id] = $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $xml = '';
            if (count($options) > 0) {
                $xml .= PHP_EOL . '<theme_options>' . PHP_EOL;
                
                foreach ($options as $option_id => $option) {
                    $type = 'string';
                    
                    if (is_array($option)) {
                        $type   = 'json';
                        $option = json_encode($option);
                    }
                    $xml .= $this->option_item($option_id, $option, $type);
                }
                
                $xml .= PHP_EOL . '</theme_options>' . PHP_EOL;
            }
            
            return array(
                'xml' => $xml,
                'attachments' => $attachments
            );
        }
        
        public function sidebars_export()
        {
            // get all sidebars 
            $sidebars              = get_option("kingdom_dynamic_sidebars", true);
            $sidebars_widgets      = get_option("sidebars_widgets", true);
            $sidebars_per_sections = get_option("kingdom_sidebars_per_sections", true);
            $attachments           = array();
            $xml                   = '';
            
            if (count($sidebars) > 0 && is_array($sidebars)) {
                $xml .= PHP_EOL . '<theme_sidebars>' . PHP_EOL;
                
                $xml .= PHP_EOL . PHP_EOL . '<sidebars>' . (json_encode($sidebars)) . '</sidebars>' . PHP_EOL;
                $xml .= PHP_EOL . PHP_EOL . '<sidebars_widgets>' . (json_encode($sidebars_widgets)) . '</sidebars_widgets>' . PHP_EOL;
                $xml .= PHP_EOL . PHP_EOL . '<sidebars_per_sections>' . (json_encode($sidebars_per_sections)) . '</sidebars_per_sections>' . PHP_EOL;
                
                $xml .= PHP_EOL . '</theme_sidebars>' . PHP_EOL;
            }
            
            return array(
                'xml' => $xml,
                'attachments' => $attachments
            );
        }
        
        private function available_widgets()
        {
            global $wp_registered_widget_controls;
            $widget_controls   = $wp_registered_widget_controls;
            $available_widgets = array();
            foreach ($widget_controls as $widget) {
                if (!empty($widget['id_base']) && !isset($available_widgets[$widget['id_base']])) { // no dupes
                    $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
                    $available_widgets[$widget['id_base']]['name']    = $widget['name'];
                }
            }
            return $available_widgets;
        }
        
        private function generate_export_data()
        {
            
            // Get all available widgets site supports
            $available_widgets = $this->available_widgets();
            
            // Get all widget instances for each widget
            $widget_instances = array();
            foreach ($available_widgets as $widget_data) {
                
                // Get all instances for this ID base
                $instances = get_option('widget_' . $widget_data['id_base']);
                
                // Have instances
                if (!empty($instances)) {
                    
                    // Loop instances
                    foreach ($instances as $instance_id => $instance_data) {
                        
                        // Key is ID (not _multiwidget)
                        if (is_numeric($instance_id)) {
                            $unique_instance_id                    = $widget_data['id_base'] . '-' . $instance_id;
                            $widget_instances[$unique_instance_id] = $instance_data;
                        }
                        
                    }
                    
                }
                
            }
            
            // Gather sidebars with their widget instances
            $sidebars_widgets          = get_option('sidebars_widgets'); // get sidebars and their unique widgets IDs
            $sidebars_widget_instances = array();
            foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {
                
                // Skip inactive widgets
                if ('wp_inactive_widgets' == $sidebar_id) {
                    continue;
                }
                
                // Skip if no data or not an array (array_version)
                if (!is_array($widget_ids) || empty($widget_ids)) {
                    continue;
                }
                
                // Loop widget IDs for this sidebar
                foreach ($widget_ids as $widget_id) {
                    
                    // Is there an instance for this widget ID?
                    if (isset($widget_instances[$widget_id])) {
                        
                        $_id = $widget_id;
                        $__  = (explode("-", $widget_id));
                        array_pop($__);
                        $__[]      = 0;
                        $widget_id = implode("-", $__);
                        
                        // Add to array
                        $sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$_id];
                    }
                }
            }
            
            // Encode the data for file contents
            $encoded_data = json_encode($sidebars_widget_instances);
            
            // Return contents
            return $encoded_data;
            
        }
        
        public function widgets_export()
        {
            $xml = PHP_EOL . '<theme_widgets>' . $this->wxr_cdata($this->generate_export_data()) . '</theme_widgets>' . PHP_EOL;
            return array(
                'xml' => $xml,
                'attachments' => $attachments
            );
        }
        
        private function option_item($name, $value, $type)
        {
            $this->item = '';
            
            if (is_array($value))
                $value = serialize($value);
            
            $value = $this->wxr_cdata($value); // export as <![CDATA[ ... ]]>
            
            $this->item .= '<item>' . PHP_EOL;
            $this->item .= '<name>' . $name . '</name>' . PHP_EOL;
            $this->item .= '<type>' . $type . '</type>' . PHP_EOL;
            $this->item .= '<value>' . $value . '</value>' . PHP_EOL;
            $this->item .= '</item>' . PHP_EOL . PHP_EOL;
            
            return $this->item;
        }
        
        public function export_wp_header($post_ids = array())
        {
            if (!is_array($post_ids))
                $post_ids = array();
            ob_start();
            the_generator('export');
?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/<?php
            echo self::WXR_VERSION;
?>/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/<?php
            echo self::WXR_VERSION;
?>/"
>
<channel>
	<title><?php
            bloginfo_rss('name');
?></title>
	<link><?php
            bloginfo_rss('url');
?></link>
	<description><?php
            bloginfo_rss('description');
?></description>
	<pubDate><?php
            echo date('D, d M Y H:i:s +0000');
?></pubDate>
	<language><?php
            bloginfo_rss('language');
?></language>
	<wp:wxr_version><?php
            echo self::WXR_VERSION;
?></wp:wxr_version>
	<wp:base_site_url><?php
            echo $this->wxr_site_url();
?></wp:base_site_url>
	<wp:base_blog_url><?php
            bloginfo_rss('url');
?></wp:base_blog_url>
<?php
            $this->wxr_authors_list($post_ids);
            $xml = ob_get_contents();
            ob_end_clean();
            
            return $xml;
        }
        public function export_wp_footer()
        {
            ob_start();
?>
</channel>
</rss>
<?php
            $xml = ob_get_contents();
            ob_end_clean();
            
            return $xml;
        }
        
        public function export_wp($args = array(), $extra_posts = array())
        {
            $attachments = array();
            ob_start();
            
            global $wpdb, $post;
            
            $defaults = array(
                'content' => 'all',
                'author' => false,
                'category' => false,
                'start_date' => false,
                'end_date' => false,
                'status' => false
            );
            $args     = wp_parse_args($args, $defaults);
            
            $sitename = sanitize_key(get_bloginfo('name'));
            if (!empty($sitename))
                $sitename .= '.';
            $filename = $sitename . 'wordpress.' . date('Y-m-d') . '.xml';
            if ('all' != $args['content']) {
                $ptype = get_post_type_object($args['content']);
                if (!$ptype->can_export)
                    $args['content'] = '__fake';
                
                $where = $wpdb->prepare("{$wpdb->posts}.post_type = %s", $args['content']);
            } else {
                $post_types = get_post_types(array(
                    'can_export' => true
                ));
                $esses      = array_fill(0, count($post_types), '%s');
                $where      = $wpdb->prepare("{$wpdb->posts}.post_type IN (" . implode(',', $esses) . ')', $post_types);
            }
            
            if ($args['status'] && ('post' == $args['content'] || 'page' == $args['content']))
                $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_status = %s", $args['status']);
            else
                $where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";
            
            $join = '';
            if ($args['category'] && 'post' == $args['content']) {
                if ($term = term_exists($args['category'], 'category')) {
                    $join = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
                    $where .= $wpdb->prepare(" AND {$wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id']);
                }
            }
            
            if ('post' == $args['content'] || 'page' == $args['content']) {
                if ($args['author'])
                    $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_author = %d", $args['author']);
                
                if ($args['start_date'])
                    $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_date >= %s", date('Y-m-d', strtotime($args['start_date'])));
                
                if ($args['end_date'])
                    $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_date < %s", date('Y-m-d', strtotime('+1 month', strtotime($args['end_date']))));
            }
            
			//$where .= $wpdb->prepare(" LIMIT %d ", 1);
			
            // Grab a snapshot of post IDs, just in case it changes during the export.
            $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} $join WHERE $where");
            
            /*
             * Get the requested terms ready, empty unless posts filtered by category
             * or all content.
             */
            $cats = $tags = $terms = array();
            if (isset($term) && $term) {
                $cat  = get_term($term['term_id'], 'category');
                $cats = array(
                    $cat->term_id => $cat
                );
                unset($term, $cat);
            } else if ('all' == $args['content']) {
                $categories = (array) get_categories(array(
                    'get' => 'all'
                ));
                $tags       = (array) get_tags(array(
                    'get' => 'all'
                ));
                
                $custom_taxonomies = get_taxonomies(array(
                    '_builtin' => false
                ));
                $custom_terms      = (array) get_terms($custom_taxonomies, array(
                    'get' => 'all'
                ));
                
                // Put categories in order with no child going before its parent.
                while ($cat = array_shift($categories)) {
                    if ($cat->parent == 0 || isset($cats[$cat->parent]))
                        $cats[$cat->term_id] = $cat;
                    else
                        $categories[] = $cat;
                }
                
                // Put terms in order with no child going before its parent.
                while ($t = array_shift($custom_terms)) {
                    if ($t->parent == 0 || isset($terms[$t->parent]))
                        $terms[$t->term_id] = $t;
                    else
                        $custom_terms[] = $t;
                }
                
                unset($categories, $custom_taxonomies, $custom_terms);
            }
            
            add_filter('wxr_export_skip_postmeta', array(
                $this,
                'wxr_filter_postmeta'
            ), 10, 2);
            
            if (count($extra_posts) > 0) {
                $post_ids = array_merge($post_ids, $extra_posts);
            }
            
            $_post_ids = array();
            if ($post_ids) {
                
                $post_ids = $this->add_attachments($post_ids);
                 
                global $wp_query;
                
                // Fake being in the loop.
                $wp_query->in_the_loop = true;
                
                // Fetch 20 posts at a time rather than loading the entire table into memory.
                while ($next_posts = array_splice($post_ids, 0, 20)) {
                    $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
                    $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");
                    
                    // Begin Loop.
                    foreach ($posts as $post) {
                    	
                        if (in_array($post->post_type, array_keys($this->search_in_post_meta))) {
                            // find if current post has specific post meta
                            foreach ($this->search_in_post_meta[$post->post_type] as $key => $value) {
                            	
                                if (!is_array($value)) {
                                    // find in top level
                                    $searched_post_meta = get_post_meta($post->ID, $value, true);
                                    if (trim($searched_post_meta) != "") {
                                        $__post_ids = explode(",", $searched_post_meta);
                                        foreach ($__post_ids as $att_id) {
                                            if ((int) $att_id > 0) {
                                                if (!in_array($att_id, $post_ids)) {
                                                    $post_ids[] = $att_id;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    // check if $value
                                    $searched_post_meta = get_post_meta($post->ID, $key, true);
                                    if ($searched_post_meta && count($searched_post_meta) > 0) {
                                        // try to find by inner key
                                        foreach ($searched_post_meta as $key2 => $value2) {
                                        	
                                            if ( in_array( $key2, (array)$value ) ) {
                                                if ((int) $value2 > 0) {
                                                	
                                                    // we need to add the media post id into main loop
                                                    if (!in_array($value2, $post_ids)) { 
                                                        $post_ids[] = $value2;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }  
  
                        setup_postdata($post);
                        $is_sticky = is_sticky($post->ID) ? 1 : 0;
                        
                        if ($post->post_type == 'attachment') {
                            $attachments[] = $post->ID;
                        }
                        
                        $_post_ids[] = $post->ID;
?>
<item>
	<title><?php
                        echo apply_filters('the_title_rss', $post->post_title);
?></title>
	<link><?php
                        the_permalink_rss();
?></link>
	<pubDate><?php
                        echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false);
?></pubDate>
	<dc:creator><?php
                        echo $this->wxr_cdata(get_the_author_meta('login'));
?></dc:creator>
	<guid isPermaLink="false"><?php
                        the_guid();
?></guid>
	<description></description>
	<content:encoded><?php
                        /**
                         * Filter the post content used for WXR exports.
                         *
                         * @since 2.5.0
                         *
                         * @param string $post_content Content of the current post.
                         */
                        echo $this->wxr_cdata(apply_filters('the_content_export', $post->post_content));
?></content:encoded>
	<excerpt:encoded><?php
                        /**
                         * Filter the post excerpt used for WXR exports.
                         *
                         * @since 2.6.0
                         *
                         * @param string $post_excerpt Excerpt for the current post.
                         */
                        echo $this->wxr_cdata(apply_filters('the_excerpt_export', $post->post_excerpt));
?></excerpt:encoded>
	<wp:post_id><?php
                        echo $post->ID;
?></wp:post_id>
	<wp:post_date><?php
                        echo $post->post_date;
?></wp:post_date>
	<wp:post_date_gmt><?php
                        echo $post->post_date_gmt;
?></wp:post_date_gmt>
	<wp:comment_status><?php
                        echo $post->comment_status;
?></wp:comment_status>
	<wp:ping_status><?php
                        echo $post->ping_status;
?></wp:ping_status>
	<wp:post_name><?php
                        echo $post->post_name;
?></wp:post_name>
	<wp:status><?php
                        echo $post->post_status;
?></wp:status>
	<wp:post_parent><?php
                        echo $post->post_parent;
?></wp:post_parent>
	<wp:menu_order><?php
                        echo $post->menu_order;
?></wp:menu_order>
	<wp:post_type><?php
                        echo $post->post_type;
?></wp:post_type>
	<wp:post_password><?php
                        echo $post->post_password;
?></wp:post_password>
	<wp:is_sticky><?php
                        echo $is_sticky;
?></wp:is_sticky>
<?php
                        if ($post->post_type == 'attachment'):
?>
	<wp:attachment_url><?php
                            echo wp_get_attachment_url($post->ID);
?></wp:attachment_url>
<?php
                        endif;
?>
<?php
                        $this->wxr_post_taxonomy();
?>
<?php
                        $postmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID));
                        foreach ($postmeta as $meta): /**
                         * Filter whether to selectively skip post meta used for WXR exports.
                         *
                         * Returning a truthy value to the filter will skip the current meta
                         * object from being exported.
                         *
                         * @since 3.3.0
                         *
                         * @param bool   $skip     Whether to skip the current post meta. Default false.
                         * @param string $meta_key Current meta key.
                         * @param object $meta     Current meta object.
                         */ 
                            if (apply_filters('wxr_export_skip_postmeta', false, $meta->meta_key, $meta))
                                continue;
?>
	<wp:postmeta>
		<wp:meta_key><?php
                            echo $meta->meta_key;
?></wp:meta_key>
		<wp:meta_value><?php
                            echo $this->wxr_cdata($meta->meta_value);
?></wp:meta_value>
	</wp:postmeta>
<?php
                        endforeach;
                        $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID));
                        foreach ($comments as $c):
?>
	<wp:comment>
		<wp:comment_id><?php
                            echo $c->comment_ID;
?></wp:comment_id>
		<wp:comment_author><?php
                            echo $this->wxr_cdata($c->comment_author);
?></wp:comment_author>
		<wp:comment_author_email><?php
                            echo $c->comment_author_email;
?></wp:comment_author_email>
		<wp:comment_author_url><?php
                            echo esc_url_raw($c->comment_author_url);
?></wp:comment_author_url>
		<wp:comment_author_IP><?php
                            echo $c->comment_author_IP;
?></wp:comment_author_IP>
		<wp:comment_date><?php
                            echo $c->comment_date;
?></wp:comment_date>
		<wp:comment_date_gmt><?php
                            echo $c->comment_date_gmt;
?></wp:comment_date_gmt>
		<wp:comment_content><?php
                            echo $this->wxr_cdata($c->comment_content);
?></wp:comment_content>
		<wp:comment_approved><?php
                            echo $c->comment_approved;
?></wp:comment_approved>
		<wp:comment_type><?php
                            echo $c->comment_type;
?></wp:comment_type>
		<wp:comment_parent><?php
                            echo $c->comment_parent;
?></wp:comment_parent>
		<wp:comment_user_id><?php
                            echo $c->user_id;
?></wp:comment_user_id>
<?php
                            $c_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID));
                            foreach ($c_meta as $meta): /**
                             * Filter whether to selectively skip comment meta used for WXR exports.
                             *
                             * Returning a truthy value to the filter will skip the current meta
                             * object from being exported.
                             *
                             * @since 4.0.0
                             *
                             * @param bool   $skip     Whether to skip the current comment meta. Default false.
                             * @param string $meta_key Current meta key.
                             * @param object $meta     Current meta object.
                             */ 
                                if (apply_filters('wxr_export_skip_commentmeta', false, $meta->meta_key, $meta)) {
                                    continue;
                                }
?>
		<wp:commentmeta>
			<wp:meta_key><?php
                                echo $meta->meta_key;
?></wp:meta_key>
			<wp:meta_value><?php
                                echo $this->wxr_cdata($meta->meta_value);
?></wp:meta_value>
		</wp:commentmeta>
<?php
                            endforeach;
?>
	</wp:comment>
<?php
                        endforeach;
?>
</item>
<?php
                    }
                }
            }
?>
		<?php
            
            $xml = ob_get_contents();
            ob_end_clean();
            return array(
                'xml' => $xml,
                'attachments' => $attachments,
                'post_ids' => $_post_ids
            );
        }
        
        private function add_attachments($post_ids)
        {
            $attachments = array();
            foreach ($post_ids as $post_id):
                $attachArgs = array(
                    'post_parent' => $post_id,
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post__not_in' => $attachments //To skip duplicates
                );
                $attachList  = get_children($attachArgs, ARRAY_A);
                $attachments = array_merge($attachments, array_keys($attachList));
            endforeach;
            
            return array_merge($attachments, $post_ids);
        }
        
        /**
         * Output list of taxonomy terms, in XML tag format, associated with a post
         *
         * @since 2.3.0
         */
        function wxr_post_taxonomy()
        {
            $post = get_post();
            
            $taxonomies = get_object_taxonomies($post->post_type);
            if (empty($taxonomies))
                return;
            $terms = wp_get_object_terms($post->ID, $taxonomies);
            
            foreach ((array) $terms as $term) {
                echo "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->wxr_cdata($term->name) . "</category>\n";
            }
        }
        
        /**
         * Wrap given string in XML CDATA tag.
         *
         * @since 2.1.0
         *
         * @param string $str String to wrap in XML CDATA tag.
         * @return string
         */
        function wxr_cdata($str)
        {
            if (seems_utf8($str) == false)
                $str = utf8_encode($str);
            
            // $str = ent2ncr(esc_html($str));
            $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';
            
            return $str;
        }
        
        /**
         * Return the URL of the site
         *
         * @since 2.5.0
         *
         * @return string Site URL.
         */
        function wxr_site_url()
        {
            // Multisite: the base URL.
            if (is_multisite())
                return network_home_url();
            // WordPress (single site): the blog URL.
            else
                return get_bloginfo_rss('url');
        }
        
        /**
         * Output list of authors with posts
         *
         * @since 3.1.0
         *
         * @param array $post_ids Array of post IDs to filter the query by. Optional.
         */
        function wxr_authors_list(array $post_ids = null)
        {
            global $wpdb;
            
            if (!empty($post_ids)) {
                $post_ids = array_map('absint', $post_ids);
                $and      = 'AND ID IN ( ' . implode(', ', $post_ids) . ')';
            } else {
                $and = '';
            }
            
            $authors = array();
            $results = $wpdb->get_results("SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and");
            foreach ((array) $results as $result)
                $authors[] = get_userdata($result->post_author);
            
            $authors = array_filter($authors);
            
            foreach ($authors as $author) {
                echo "\t<wp:author>";
                echo '<wp:author_id>' . $author->ID . '</wp:author_id>';
                echo '<wp:author_login>' . $author->user_login . '</wp:author_login>';
                echo '<wp:author_email>' . $author->user_email . '</wp:author_email>';
                echo '<wp:author_display_name>' . $this->wxr_cdata($author->display_name) . '</wp:author_display_name>';
                echo '<wp:author_first_name>' . $this->wxr_cdata($author->user_firstname) . '</wp:author_first_name>';
                echo '<wp:author_last_name>' . $this->wxr_cdata($author->user_lastname) . '</wp:author_last_name>';
                echo "</wp:author>\n";
            }
        }
        
        function wxr_filter_postmeta($return_me, $meta_key)
        {
            if ('_edit_lock' == $meta_key)
                $return_me = true;
            return $return_me;
        }
        
        private function xmlpp($xml, $html_output = false)
        {
            $xml_obj = new SimpleXMLElement($xml);
            $level   = 4;
            $indent  = 0; // current indentation level  
            $pretty  = array();
            
            // get an array containing each XML element  
            $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));
            
            // shift off opening XML tag if present  
            if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
                $pretty[] = array_shift($xml);
            }
            
            foreach ($xml as $el) {
                if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                    // opening tag, increase indent  
                    $pretty[] = str_repeat(' ', $indent) . $el;
                    $indent += $level;
                } else {
                    if (preg_match('/^<\/.+>$/', $el)) {
                        $indent -= $level; // closing tag, decrease indent  
                    }
                    if ($indent < 0) {
                        $indent += $level;
                    }
                    $pretty[] = str_repeat(' ', $indent) . $el;
                }
            }
            $xml = implode("\n", $pretty);
            return ($html_output) ? htmlentities($xml) : $xml;
        }
        
        public function export()
        {
            $params = isset($_REQUEST['params']) ? $_REQUEST['params'] : '';
            parse_str($params, $params);
            
            $options = array();
            
            if (isset($params['theme-options'])) {
                $options[] = $this->options_export();
            }
            if (isset($params['sidebars'])) {
                $options[] = $this->sidebars_export();
            }
            if (isset($params['widgets'])) {
                $options[] = $this->widgets_export();
            }
            
            // get the options attachaments 
            $options_attachments = array();
            if (count($options) > 0) {
                foreach ($options as $option) {
                    if (isset($option['attachments']) && is_array($option['attachments']) && count($option['attachments']) > 0) {
                        foreach ($option['attachments'] as $att) {
                            if (!in_array($att, $options_attachments)) {
                                $options_attachments[] = $att;
                            }
                        }
                    }
                }
            }
            
            if (is_array($params) && count($params) > 0) {
                
                $post_types = array();
                foreach ($params as $key => $value) {
                    if (in_array($key, array(
                        'download',
                        'theme-options',
                        'all',
                        'sidebars',
                        'widgets'
                    )))
                        continue;
                    $post_types[] = $key;
                }
                
                // HACK: if have attachments, need to take it 
                if (count($options_attachments) > 0) {
                    $post_types[] = '__fake_pt';
                }
                
                if (count($post_types) > 0) {
                    
                    $export      = array();
                    $attachments = array();
                    $post_ids    = array();
                    
                    foreach ($post_types as $post) {
                        
                        $items = $this->export_wp(array(
                            'content' => $post
                        ), $options_attachments);
                        
                        $export[]      = $items['xml'];
                        $post_ids[]    = $items['post_ids'];
                        $attachments[] = $items['attachments'];
                    }
                }
                 
                if (count($attachments) > 0) {
                    $_attachments = $attachments;
                    $attachments  = array();
                    foreach ($_attachments as $key => $value) {
                        if (count($value) > 0) {
                            foreach ($value as $key2 => $value2) {
                                if (!in_array($value2, $attachments)) {
                                    $attachments[] = $value2;
                                }
                            }
                        }
                    }
                }
                
                if (count($post_ids) > 0) {
                    $_post_ids = $post_ids;
                    $post_ids  = array();
                    foreach ($_post_ids as $key => $value) {
                        if (count($value) > 0) {
                            foreach ($value as $key2 => $value2) {
                                if (!in_array($value2, $post_ids)) {
                                    $post_ids[] = $value2;
                                }
                            }
                        }
                    }
                }
                
                $multi = (is_multisite()) ? '-' . end(explode('/', site_url())) : '';
                
                $this->config['export_file'] = '/aa-backup-manager/' . (date('G_m_d_y')) . '_backup' . $multi . '.zip';
                
                $xml_file_content = $this->export_wp_header($post_ids);
                if (count($options) > 0) {
                    foreach ($options as $option) {
                        if (isset($option['xml']) && trim($option['xml']) != "") {
                            $xml_file_content .= $option['xml'];
                        }
                    }
                }
                
                if (count($export) > 0) {
                    $xml_file_content .= implode("\n", $export);
                }
                $xml_file_content .= $this->export_wp_footer();
                
                $xml_file = BKM_manager()->path('UPLOAD_BASE_DIR', 'backup.xml');
                
                // Debug
                if( 0 ){
	                $new_file_content = $this->xmlpp($xml_file_content);
	                header("Content-Type:text/plain");
	                die($new_file_content);
				}
                
                
                $new_file_content = ($xml_file_content);
                BKM_manager()->wp_filesystem->put_contents($xml_file, $new_file_content);
                
                // check if backup file exist and delete old backup
                if (is_file(BKM_manager()->path('UPLOAD_BASE_DIR', $this->config['export_file']))) {
                    @unlink(BKM_manager()->path('UPLOAD_BASE_DIR', $this->config['export_file']));
                }
                
                // start the backup archive
                $this->backup_archive = new BKM_create_archive(BKM_manager()->path('UPLOAD_BASE_DIR', $this->config['export_file']));
                
                // add xml file into backup
                $this->backup_archive->add_file($xml_file, basename($xml_file));
                
                $this->attachments_backup($attachments);
                
                $result = array(
                    'status' => 'valid',
                    'file_url' => BKM_manager()->path('UPLOAD_BASE_URL', $this->config['export_file']),
                    'msg' => 'success'
                );
                
                $html = array();
                if (is_wp_error($xml_file)) {
                    $html[] = '
				    <h3>Export Failed</h3>
				    <p>';
                    __('Failed to export backup file', 'wordpress-importer');
                    $html[] = ': ' . $this->export_file->get_error_message();
                    $html[] = '</p>';
                } else {
                    
                    $html[] = '	
					<h3>Export Done</h3>
					<p>
						Your exported folder is located here: <code>{' . (BKM_manager()->path('UPLOAD_BASE_DIR', 'aa-backup-manager')) . '}</code> and you can download the export file from <a style="position: relative; bottom: -6px;" class="diet_nutrition_theme-button small green" href="' . (BKM_manager()->path('UPLOAD_BASE_URL', $this->config['export_file'])) . '" target="_blank">here</a>
					</p>
					<p>
						Once you\'ve saved this folder on your computer (via FTP) OR from your Web Browser (google chome, firefox,  ... etc.) you can upload into another wordpress install to import all your data in the new website.
					</p>';
                }
                
                $result['html'] = implode("\n", $html);
                
                if (count($_POST) == 0) {
                    var_dump('<pre>', $result, '</pre>');
                    die;
                }
                
                die(json_encode($result));
            }
        }
        
        private function human_filesize($bytes, $decimals = 2)
        {
            $size   = array(
                'B',
                'kB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
                'YB'
            );
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        }
        
        private function attachments_backup($attachments = array())
        {
            if (count($attachments) > 0) {
                // Fetch 20 posts at a time rather than loading the entire table into memory.
                while ($next_posts = array_splice($attachments, 0, 20)) {
                    $where = 'WHERE post_id IN (' . implode(',', $next_posts) . ') and meta_key="_wp_attached_file"';
                    $posts = $this->parent->db->get_results("SELECT * FROM {$this->parent->db->postmeta} $where", ARRAY_A);
                    
                    // Begin Loop.
                    foreach ($posts as $post) {
                        // check if file exists
                        if (file_exists(BKM_manager()->path('UPLOAD_BASE_DIR', $post['meta_value']))) {
                            $this->backup_archive->add_file(BKM_manager()->path('UPLOAD_BASE_DIR', $post['meta_value']), $post['meta_value']);
                        }
                    }
                }
            }
        }
        
        private function print_error($msg = "")
        {
            //!!!TMP
            echo ($msg);
        }
    }
}