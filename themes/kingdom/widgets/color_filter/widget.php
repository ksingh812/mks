<?php 
// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
    die('Direct script access not allowed');
}

if(class_exists('kingdom_color_filter') != true) 
{
	class kingdom_color_filter extends WP_Widget 
	{
		private $the_theme = null;
		private $the_widget = array();
		private $alias = 'color_filter';
		
	    private $default_color_config = array(
	        'title' => ''
	    );
	    
	    public function __construct() 
	    {
			global $kingdom;
			//  
			$this->the_theme = $kingdom;
			$this->the_widget = $this->the_theme->cfg['widgets'][$this->alias];
			
			if( isset($this->the_widget) && count($this->the_widget) > 0 ){
				$widget_ops = array(
		            'classname'   => 'widget_' . $this->the_theme->alias . '_' . $this->alias, 
		            'description' => $this->the_widget[$this->alias]['description']
		        );
		        parent::__construct( $this->the_theme->alias . '-' . $this->alias, $this->the_theme->alias . ' - ' . $this->the_widget[$this->alias]['title'], $widget_ops);
			}
	    }

	    public function widget( $args, $instance ) 
	    {
	    	global $kingdom;
			
	        extract( $args );
			
			$values = get_option( 'widget_' . ( $this->the_theme->alias ) . '-' . $this->alias );
			if( isset($values["_multiwidget"]) ) unset($values["_multiwidget"]);
			$values = $values[key($values)]; 
			?>
			<div class="kingdom-widget kd_color_filter">
				<h3><?php echo isset($values['title']) ? $values['title'] : '';?></h3>
			<?php
				// get current tag 
				$original_tags = get_query_var('pa_color');
				 
				$tag_url_base = home_url('?pa_color=');
				$is_rew = false;
				if ( get_option('permalink_structure') != '' ) {
					$is_rew = true;
					$tag_url_base = home_url('color/');
				}
				
				$tags = array();
				if(trim($original_tags) != ""){
					$tags = explode(",", $original_tags);
				}
				
				$retHtml = array();
				//$colors = get_terms( 'product_tag', 'hide_empty=0' );
				$colors = get_terms( 'pa_color', 'hide_empty=0' );
				//var_dump('<pre>',$colors,'</pre>');  
				if(count($colors) > 0){
					foreach ($colors as $key => $value){
					
						// check if is color 
						$__color = $kingdom->isValidColorName($value->name);
						if( !$__color === false ){
							if(count($__color) > 0){
								$rgb = "rgb(" . ( implode(",", $__color) ) . ")";
							}
							
							// term link 
							$term_link = $tag_url_base . $original_tags;
							
							if(in_array($value->slug, $tags)) {
								
								$_tags = $tags;
								foreach ($_tags as $key2 => $value2) {
									if ($value->slug === $value2) {
										unset($_tags[$key2]);
									}
								}
								
								// remove the tag from link 
								$term_link = $tag_url_base . implode(',', $_tags) . ( $is_rew === true ? '/' : '');
								
								$retHtml[str_replace(" ", "", $value->name)] = '<a class="kingdom-box is_select" href="' . ( $term_link ) . '" style="background-color: ' . ( $rgb ) . ';"><span></span>' . ( $value->name ) . '</a>';
							}else{
								
								// add extra tag only if need 
								$term_link .=  ( count($tags) > 0 ? ',' : '' ) . $value->slug . ( $is_rew === true ? '/' : '');
								
								$retHtml[str_replace(" ", "", $value->name)] = '<a class="kingdom-box" href="' . ( $term_link ) . '" style="background-color: ' . ( $rgb ) . ';"><span></span>' . ( $value->name ) . '</a>';
							}
						}
					}
				}
				
				if(count($retHtml) > 0){
					$color_config = $kingdom->getAllSettings('array', 'color_config');
					if( trim($color_config['colors_name']) != ""){
						$color_name_str = $color_config['colors_name'];
						
						// trim by row
						$_ = explode("\n", $color_name_str);
						$colors = array();
						if(count($_) > 0){
							foreach ($_ as $key => $value){
								$value = str_replace(" ", "", $value);
								$__ = explode("=>", $value);
								if(count($__) > 0){
									$colors[trim($__[0])] = explode(",", str_replace(" ", "", trim($__[1])));
								}
							}
						}
						$checkArr = array_keys( $colors );
					}	
					
					if(count($checkArr) > 0){
						foreach ($checkArr as $key => $value){
							
							$toPrint = $retHtml[$value];
							
							// fix if need to remove error for // 
							$toPrint = str_replace('product-tag//', 'shop/', $toPrint);
							
							echo $toPrint . PHP_EOL; 
						}
					}
					
					echo '<div style="clear:both;"></div>';
				}
			?>
			</div>
			<?php 
	    }
	    
	    public function parse_output($instance)
	    {
	        $html = array();
	        
	        return implode("\n", $html);
	    }
	    
	    public function update( $new_instance, $old_instance )
	    {   
	        $instance = $old_instance;
	        // Strip tags from title and name to remove HTML 
	        if( count($this->the_widget[$this->alias]['options']) > 0 ){
	        	foreach ($this->the_widget[$this->alias]['options'] as $key => $value) {
					$instance[$key] = esc_html( $_REQUEST[$key] );  
				}
	        } 
			
	        return $instance;
	    }
	
	    public function form( $instance ) 
	    {
	    	echo $this->the_theme->print_widget_fields( $this->the_widget[$this->alias]['options'], $instance );
	    }
	}
  
	// register the widgets
	add_action( 'widgets_init', create_function( '', 'return register_widget("kingdom_' . ( $current_widget ) . '");' ) );
}