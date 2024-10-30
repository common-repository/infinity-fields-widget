<?php

/*
*Plugin Name: Infinity Fields Widget
*Plugin URI: https://github.com/Bonus3/Infinity-Fields-Widget
*Description: Widget to add infinity custom fields. How to use https://github.com/Bonus3/Infinity-Fields-Widget
*Version: 1.0
*Author: Anderson Gonçalves (Bônus)
*Author URI: https://github.com/Bonus3
*Licence: GPL2
*Text Domain: infinity-fields-widget
*Domain Path: languages/
*/

if (!defined('ABSPATH')) { exit; }

/* Define plugin of directory */
$ifw_dir = plugins_url('', __FILE__) . '/';


/* Load text domain */
add_action('init', 'ifw_load_textdomain');
function ifw_load_textdomain() {
    load_plugin_textdomain('infinity-fields-widget', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

 
 /*
  * Insert JavaScript file in queue
  */
add_action('admin_enqueue_scripts', 'ifw_register_scripts');
function ifw_register_scripts() {
    global $ifw_dir;
    wp_enqueue_script('ifw_script', $ifw_dir . 'js/infinity-fields-widget.js', array('jquery'), '1.0', true);
}

/* Cretae class IFW_Widget */
if (!class_exists('IFW_Widget')) {
    class IFW_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct('ifw', //ID
                    'Infinity Fields Widget', //Name
                    array(
                        //Description
                        'description' => __('Widget to custom infinity fields', 'infinity-fields-widget')
                    ));
        }
        
        /* Form in the widget page */
        public function form($instance) {
            /*
             * Title field
             * This is used to name the container widget
             */
            $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', 'infinity-fields-widget' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <?php
                /*
                 * End title field
                 */
            
                /*
                 * Below, a container with the class 'ifw', where it contains 
                 * every dynamic fields.
                 * 
                 * Dynamic fields are compounds a label and a value
                 * The structure HTML of the Dynamic fields is: 
                 * 
                 * div
                 * ----p
                 * --------label
                 * --------input
                 * ----p
                 * --------label
                 * --------input
                */
            ?>
            <div class="ifw" style="float:left;width:100%">
            <?php
            /*
             * $i is the count of interactions
             * $a is the count of dynamic fields
             */
            $i = $a = 0;
            
            
            /*
             * If has informations saved, is created a Dynamic field (with a label and a value) 
             * The informations are saved in serialized.
             * To know what is label and what is value, 
             * for each dynamic field are saved the label first and after the value.
             * 
             * Example of how to get:
             * 
             * array(
             *      'title' => 'Title of section widget',
             *      'label1' => 'Name label 1',
             *      'value1' => 'value 1',
             *      'label2' => 'Name label 2',
             *      'value2' => 'value 2',
             *      'label3' => 'Name label 3',
             *      'value3' => 'value 3'
             * )
             * 
             */
            if (count($instance)) {
                unset($instance['title']); //Remove title
                foreach ($instance as $key => $value) :  //For each the elements?>
                    <?php
                        /*
                         *if $i is even, the next value is the label field
                         */
                        if (($i % 2) === 0) {
                            $label = 'Label'; //Set label of the next field to 'Label'
                            echo "<div>"; //open container that will contain the paragraphs 
                        } else { // if $i is odd, the next value is the value field
                            $label = 'Value'; //Set label of the next field to 'Label'
                        }
                    ?>
                        <p style="float:left;width:48%;margin:2px 1%;">
                            <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo sprintf(__( $label , 'infinity-fields-widget' ).' <span class="ifw-label">%d</span>:', $a + 1); ?></label> 
                            <input class="widefat" id="<?php echo $this->get_field_id( $key ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
                        </p>
                    <?php
                        /*
                         *if $i is over
                         */
                        if (($i % 2) === 1) {
                            $a++; //increment number dynamic fields
                            echo "</div>"; //close container
                        }
                    ?>
                <?php
                $i++; //Increment interactions
                endforeach;
            } else {
                /*
                 * How the fields are created dynamically by user,
                 * was need create random an ID for each field
                 * 
                 */
            $random_id_label = md5(uniqid(rand(), true));  //Random ID label
            $random_id_value = md5(uniqid(rand(), true)); //Ramdom ID value
            ?>
                <div>
                    <p style="float:left;width:48%;margin:2px 1%;">
                        <label for="<?php echo $this->get_field_id( $random_id_label ); ?>"><?php echo sprintf(__( 'Label' , 'infinity-fields-widget' ).' <span class="ifw-label">%d</span>:', $a + 1); ?></label> 
                        <input class="widefat" id="<?php echo $this->get_field_id( $random_id_label ); ?>" name="<?php echo $this->get_field_name( $random_id_label ); ?>" type="text" value="">
                    </p>
                    <p style="float:left;width:48%;margin:2px 1%;">
                        <label for="<?php echo $this->get_field_id( $random_id_value ); ?>"><?php echo sprintf(__( 'Value' , 'infinity-fields-widget' ).' <span class="ifw-label">%d</span>:', $a + 1); ?></label> 
                        <input class="widefat" id="<?php echo $this->get_field_id( $random_id_value ); ?>" name="<?php echo $this->get_field_name( $random_id_value ); ?>" type="text" value="">
                    </p>
                </div>
            <?php
            }
            // The link below is to add dynamic field
            ?>
            <a href="#" class="ifw-add button button-primary" onclick="return ifw_add_field(this)"><?php _e('Add', 'infinity-fields-widget'); ?></a>
            </div>
        <?php } // End function form
        
        
        /*
         * Update data 
         */
        public function update($new_instance, $old_instance) {
            $instance = array(); // Instance to be returned
            
            //For each field, remove tags HTML
            foreach ($new_instance as $key => $value) {
                $instance[$key] = strip_tags($value);
            }
            return $instance;
        } //End function update
        
        
        /*
         * HTML rendered to front-end
         */
        public function widget($args, $instance) {
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) { // Title of section widget
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
            unset($instance['title']); //Remove title for list the labels and the values
            
            /*
            * The widget is rendered in form list no ordened
            */
            echo "<ul>";
            $i = 0; //The count of interactions
            foreach ($instance as $value) {
                /*
                * If the interaction is even:
                * - Open item of list
                * - The class assigned to the span is 'ifw-label'
                */
                
                /*
                * If the interaction is odd:
                * - Close item of list
                * - The class assigned to the span is 'ifw-value'
                */
                if (($i % 2) === 0) { echo '<li>'; } 
                echo "<span class='ifw-" . (($i % 2) === 0 ? 'label' : 'value') . "'>$value" . (($i % 2) === 0 ? ':' : '') . "</span>";
                if (($i % 2) === 1) { echo '</li>'; } // If the interaction is odd, close item list
                $i++; //Increment the count of interactions
            }
            echo "</ul>"; //Close list
            echo $args['after_widget'];
        } //End function widget
    }
    
    //Register widget
    add_action('widgets_init', function () {
        register_widget('IFW_Widget');
    });
    
    /*
     * Function to be used in the front-end 
     * 
     * Return an array two-dimensional in the form, if desired:
     * 
     * array(
     *      array(
     *          'label' => 'Label 1',
     *          'value' => 'Value 1'
     *      ),
     *      array(
     *          'label' => 'Label 2',
     *          'value' => 'Value 2'
     *      ),
     *      array(
     *          'label' => 'Label N',
     *          'value' => 'Value N'
     *      )
     * )
     */
    function ifw_get_fields() {
        global $wp_widget_factory;
        $ifw = $wp_widget_factory->widgets['IFW_Widget'];
        $option = get_option('widget_ifw');
        $option = $option[$ifw->number];
        unset($option['title']);
        
        $i = $a = 0;
        $opts = array();
        foreach ($option as $value) {
            if (($i % 2) === 0) {
                $opts[$a]['label'] = $value;
            } else {
                $opts[$a]['value'] = $value;
                $a++;
            }
            $i++;
        }
        return $opts;
    } //End function ifw_get_fields()
}