<?php 
/*
Plugin Name: Category Sisters
Plugin URI: 
Description: Show Category sisters
Author: La Petite Chambre Noire 
Version: 1.0
Author URI: http://www.lapetitechambrenoire.fr/
*/

class Category_Sisters extends WP_Widget {

	function Category_Sisters() {
		/* Widget settings. */
    	$widget_ops = array(
      		'classname' => 'cat-sisters',
      		'description' => 'Show category Sisters Links on post & category page only');
		
		/* Widget control settings. */
		$control_ops = array(
			'width' => 250,
			'height' => 250,
			'id_base' => 'cat-sisters-widget');
		
		/* Create the widget. */
		$this->WP_Widget('cat-sisters-widget', 'Category Sisters', $widget_ops, $control_ops );
	}
	
	function form ($instance) {
		// prints the form on the widgets page
		$defaults = array('title'=>'','count'=>'', 'empty'=>'');
    	$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    	
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
    <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="20">
  </p>
  <p>
   <input type="checkbox" id="<?php echo $this->get_field_id('empty'); ?>" name="<?php echo $this->get_field_name('empty'); ?>" <?php if ($instance['empty']) echo 'checked="checked"' ?> />
   <label for="<?php echo $this->get_field_id('empty'); ?>">Show Empty Cats ?</label>
  </p>
  <p>
   <input type="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" <?php if ($instance['count']) echo 'checked="checked"' ?> />
   <label for="<?php echo $this->get_field_id('count'); ?>">Show Counts ?</label>
  </p>

	<?php }
	

	function update ($new_instance, $old_instance) {
		// used when the user saves their widget options
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['empty'] = $new_instance['empty'];
		$instance['count'] = $new_instance['count'];
		
		return $instance;
	}

	function widget ($args,$instance) {
		extract($args);
		
		$title = $instance['title'];
		$count = $instance['count'];
		$empty = $instance['empty'];
 		
 		global $wpdb;
		if(is_category() ) {
			$thiscat = get_term( get_query_var('cat') , 'category' ); 
		} elseif(is_single() ) {
			$aCats = get_the_category();
			$thiscat = $aCats[0];
  		}
  		$out = '';
  		if($thiscat) {
  			$args = array();
  			if($empty) {
  				$args['hide_empty'] = false;
  			}
  			$args["parent"] = $thiscat->term_id;
  			$subcategories = get_terms( 'category' , $args);  
  
			if(empty($subcategories) && $thiscat->parent != 0) {  
				$args["parent"] = $thiscat->parent;
    			$subcategories = get_terms( 'category' , $args );  
			}  
  
			$items='';  
			if(!empty($subcategories)) {  
    			foreach($subcategories as $subcat) {  
        			if($thiscat->term_id == $subcat->term_id) $current = ' current-cat'; else $current = '';  
        			$items .= '<li class="cat-item cat-item-'.$subcat->term_id.$current.'">';
        			$items .= '<a href="'.get_category_link( $subcat->term_id ).'" title="'.$subcat->name.'">'.$subcat->name.'</a>';  
        			if($count) {
        				$items .= ' ('.$subcat->count.')';
        			}
        			$items .= '</li>';  	
    			}  
    			$out = "<ul>$items</ul>";  
			}  
			unset($subcategories,$subcat,$thiscat,$items); 
  		}
  		if(isset($out) && $out != '') {
  			echo $before_widget;
  			if(isset($title) && $title != '') 
  				echo $before_title.$title.$after_title;
  			echo $out;
  			echo $after_widget;
  		}
  	}
}

// initiate the widget
function lpch_load_widgets() {
  register_widget('Category_Sisters');
}

// register the widget
add_action('widgets_init', 'lpch_load_widgets');

?>