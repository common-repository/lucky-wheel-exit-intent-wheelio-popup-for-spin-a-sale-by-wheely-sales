<?php
    /*
     Plugin Name: Lucky Wheel Exit Intent Wheelio Popup for Spin a Sale by Wheely Sales
     Plugin URI: https://wheelysales.com/?utm_source=wordpress-plugin
     Description: Wheel of Fortune exit popup to achieve higher email opt-in rates. Wheely Sales consistently outperforms all other lead capturing tools. To get started: activate the Wheely Sales plugin and then go to your Wheely Sales Settings page to set up your wheel ID.
     Text Domain: wheely-sales
     Author: Wheely Sales
     Version: 1.0.0
     Tested up to: 5.1.1
     License: GPLv2 or later
     */
    
    /*
     Copyright 2019 Wheely Sales (email : josh@wheelysales.com)
     
     This program is free software; you can redistribute it and/or
     modify it under the terms of the GNU General Public License
     as published by the Free Software Foundation; either version 2
     of the License, or (at your option) any later version.
     
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
     
     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
     */
    
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // exit if accessed directly!
    }
    
    class Wheely_Sales_Plugin {
        
        var $path;                 // path to plugin dir
        var $wp_plugin_page;     // url to plugin page on wp.org
        var $ns_plugin_page;     // url to pro plugin page on ns.it
        var $ns_plugin_name;     // friendly name of this plugin for re-use throughout
        var $ns_plugin_menu;     // friendly menu title for re-use throughout
        var $ns_plugin_slug;     // slug name of this plugin for re-use throughout
        var $ns_plugin_ref;     // reference name of the plugin for re-use throughout
        
        function __construct(){
            $this->path = plugin_dir_path( __FILE__ );
            
            $this->wp_plugin_page = "http://wordpress.org/plugins/wheely-sales";
            
            $this->ns_plugin_page = "https://wheelysales.com/?utm_source=wordpress-plugin";
            
            $this->ns_plugin_name = "Wheely Sales";
            
            $this->ns_plugin_menu = "Wheely Sales";
            
            $this->ns_plugin_slug = "wheely-sales";
            
            $this->ns_plugin_ref = "wheely-sales";
            
            add_action( 'plugins_loaded', array($this, 'setup_plugin') );
            add_action( 'admin_notices', array($this,'admin_notices'), 11 );
            add_action( 'network_admin_notices', array($this, 'admin_notices'), 11 );
            add_action( 'admin_init', array($this,'register_settings_fields') );
            add_action( 'admin_menu', array($this,'register_settings_page'), 20 );
            add_action( 'admin_enqueue_scripts', array($this, 'admin_assets') );
            add_action( 'wp_footer', array($this, 'getWheelySalesScript') );
            
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'add_plugin_page_settings_link') );
            
        }
        
        
        /*********************************
         * NOTICES & LOCALIZATION
         */
        
        function setup_plugin(){
            load_plugin_textdomain( $this->ns_plugin_slug, false, $this->path."lang/" );
        }
        
        function admin_notices(){
            $message = '';
            if ( $message != '' ) {
                echo "<div class='updated'><p>$message</p></div>";
            }
        }
        
        function admin_assets($page){
            wp_register_style( $this->ns_plugin_slug, plugins_url("css/ns-custom.css",__FILE__), false, '1.0.0' );
            wp_register_script( $this->ns_plugin_slug, plugins_url("js/ns-custom.js",__FILE__), false, '1.0.0' );
            if( strpos($page, $this->ns_plugin_ref) !== false  ){
                wp_enqueue_style( $this->ns_plugin_slug );
                wp_enqueue_script( $this->ns_plugin_slug );
            }
        }
        

        function add_plugin_page_settings_link( $links ) {
            $links[] = '<a href="' .
            admin_url( 'options-general.php?page=wheely-sales' ) .
            '">' . __('Settings') . '</a>';
            return $links;
        }
        
        /**********************************
         * SETTINGS PAGE
         */
        
        function register_settings_fields() {
            add_settings_section(
                                 $this->ns_plugin_ref.'_set_section',     // ID used to identify this section and with which to register options
                                 '',                     // Title to be displayed on the administration page
                                 false,                                     // Callback used to render the description of the section
                                 $this->ns_plugin_ref                     // Page on which to add this section of options
                                 );
            add_settings_field(
                               $this->ns_plugin_ref.'_field1',     // ID used to identify the field
                               'Wheel ID:',                     // The label to the left of the option interface element
                               array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
                               $this->ns_plugin_ref,                 // The page on which this option will be displayed
                               $this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
                               array(                                 // args to pass to the callback function rendering the option interface
                                     'field_name' => $this->ns_plugin_ref.'_field1'
                                     )
                               );
            register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_field1');
        }
        
        function show_settings_field($args){
            $saved_value = get_option( $args['field_name'] );
            // initialize in case there are no existing options
            if ( empty($saved_value) ) {
                echo '<input style="width: 200px;" type="text" name="' . $args['field_name'] . '" placeholder="eg. jkqJQwS5DNHrrWSLX" />';
            } else {
                echo '<input style="width: 200px;" type="text" name="' . $args['field_name'] . '" value="'.esc_attr($saved_value).'" />';
            }
        }
        
        function register_settings_page(){
            add_submenu_page(
                             'options-general.php',                                // Parent menu item slug
                             __($this->ns_plugin_name, $this->ns_plugin_name),    // Page Title
                             __($this->ns_plugin_menu, $this->ns_plugin_name),    // Menu Title
                             'manage_options',                                    // Capability
                             $this->ns_plugin_ref,                                // Menu Slug
                             array( $this, 'show_settings_page' )                // Callback function
                             );
        }
        
        function show_settings_page(){
            ?>
<div class="wrap">

<h2><?php $this->wheely_sales_plugin_image( 'banner.png', __('ALT') ); ?></h2>

<!-- BEGIN Left Column -->
<div class="ns-col-left">

<div style="display: flex; align-items: center;">
<h3>1. Create your Wheely Sales account:</h3>
<a style="margin-left: 20px" href="https://app.wheelysales.com/" target="_blank">
<input type="button" class="button-primary" style="background: green;" value="Wheely Sales Account"/>
</a>
</div>

<h3 style="margin-top: 60px; margin-bottom: 0px">2. Copy and paste your Wheel ID:</h3>

<form method="POST" action="options.php" style="width: 100%; padding-left: 20px;">
<?php settings_fields($this->ns_plugin_ref); ?>
<?php do_settings_sections($this->ns_plugin_ref); ?>
<?php submit_button(); ?>
</form>
</div>
<!-- END Left Column -->

<!-- BEGIN Right Column -->
<div class="ns-col-right">
<h3>Thanks for using Wheely Sales</h3>
<p>For more information visit: <a href="https://wheelysales.com/?utm_source=wordpress-plugin" target="_blank">wheelysales.com</a></p>
<p>Any issues just contact: <a href="mailto:josh@wheelysales.com">josh@wheelysales.com</a></p>
</div>
<!-- END Right Column -->

</div>
<?php
    }
    
    
    /*************************************
     * UITILITY
     */
    
    function wheely_sales_plugin_image( $filename, $alt='', $class='' ){
        echo "<img style='margin-bottom: 40px; border-radius: 10px' src='".plugins_url("/images/$filename",__FILE__)."' alt='$alt' class='$class' />";
    }
    
    function getWheelySalesScript() {
        
        $wheelId = get_option('wheely-sales_field1', '');
        
        if ($wheelId !== '') {
            echo '<script id="wheelscript" src="https://app.wheelysales.com/wheel/" type="text/javascript" wheelHex="'.$wheelId.'" defer></script>';
        }
    }
    
    }
    
    new Wheely_Sales_Plugin();
