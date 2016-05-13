<?php 
// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
    die('Direct script access not allowed');
}

if(class_exists('kingdom_custom_text') != true) 
{
	class kingdom_custom_text extends WP_Widget 
	{
		private $the_theme = null;
		private $the_widget = array();
		private $alias = 'custom_text';
		
	    private $default_config = array(
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
	        extract( $args );
			
			$values = get_option( 'widget_' . ( $this->the_theme->alias ) . '-' . $this->alias );
			if( isset($values["_multiwidget"]) ) unset($values["_multiwidget"]);
			$values = $values[key($values)]; 
			?>
			<!-- Custom Text Widget -->
			<div class="custom-widget">
				<?php echo str_replace( "\\", '', html_entity_decode($values['text']) );?>
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