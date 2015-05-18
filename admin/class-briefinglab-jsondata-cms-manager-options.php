<?php

class Briefinglab_JsonData_CMS_Manager_Options {

    private $version;

    private $options;

    private $js_configuration;

    function __construct($version, $options) {
        $this->version = $version;
        $this->options = $options;
        if(!WP_DEBUG) {
            $this->js_configuration['css_path'] = BRIEFINGLAB_JSONDATA_CMS_CSS_PROD_PATH;
            $this->js_configuration['js_path'] = BRIEFINGLAB_JSONDATA_CMS_JS_PROD_PATH;
            $this->js_configuration['css_extension'] = $this->version . 'min.css';
            $this->js_configuration['js_extension'] = $this->version . 'min.js';
        }else{
            $this->js_configuration['css_path'] = BRIEFINGLAB_JSONDATA_CMS_CSS_PATH;
            $this->js_configuration['js_path'] = BRIEFINGLAB_JSONDATA_CMS_JS_PATH;
            $this->js_configuration['css_extension'] = 'css';
            $this->js_configuration['js_extension'] = 'js';
        }
    }

    public function register_scripts() { 
        wp_register_script( 'briefinglab-jsondata-cms-admin-js', plugins_url( $this->js_configuration['js_path'] . 'briefinglab-jsondata-cms-admin.' . $this->js_configuration['js_extension'], __FILE__ ) );
        wp_register_style( 'briefinglab-jsondata-cms-admin-css', plugins_url( $this->js_configuration['css_path'] . 'briefinglab-jsondata-cms-admin.' . $this->js_configuration['css_extension'], __FILE__ ) );
    }

    // echo '<div class="debug_"><pre>'.print_r($this->options,true).'</pre></div>';

    public function enqueue_scripts($hook) { 
      $post_types = explode('|||',$this->options['briefinglab-jsondata-cms-post-type']);
        if(empty($post_types) || ( in_array($hook,array('post.php','post-new.php')) && in_array(get_post_type(),$post_types))){
            wp_enqueue_script('briefinglab-jsondata-cms-admin-js');
            wp_enqueue_style('briefinglab-jsondata-cms-admin-css');
            
        }
    }

    function add_plugin_options_page() {
        add_options_page(
            'WP JsonData CMS options',
            __('JsonData CMS Options', 'briefinglab-jsondata-cms'),
            'manage_options',
            'briefinglab-jsondata-cms-options',
            array( $this, 'render_admin_options_page' )
        );
    }

    function render_admin_options_page() {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e( 'WP JsonData CMS options', 'briefinglab-jsondata-cms' )?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'briefinglab-jsondata-cms-options' );
                do_settings_sections( 'briefinglab-jsondata-cms-options' );
                submit_button();
                ?>
            </form>
        </div>
    <?php 
    }

    function options_page_init() {
        register_setting(
            'briefinglab-jsondata-cms-options', // Option group
            'briefinglab-jsondata-cms-options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'briefinglab-jsondata-cms-options', // ID
            __('General settings', 'briefinglab-jsondata-cms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'briefinglab-jsondata-cms-options' // Page
        );

        add_settings_field(
            'briefinglab-jsondata-cms-post-type',
            __( 'Post type', 'briefinglab-jsondata-cms' ),
            array( $this, 'post_type_callback' ),
            'briefinglab-jsondata-cms-options',
            'briefinglab-jsondata-cms-options'
        );

    }

    public function print_section_info()
    {
        //_e( 'Enter your settings below:', 'briefinglab-jsondata-cms' );
    }

    function sanitize( $input ) {

        foreach ($input as $key => $value){
            if( ! is_array( $value ) )
                $input[$key] =  sanitize_text_field($value);
        }

        if( $input['briefinglab-jsondata-cms-post-type'] )
            $input['briefinglab-jsondata-cms-post-type'] = implode( '|||', $input['briefinglab-jsondata-cms-post-type'] );

        return $input;
    }

    public function post_type_callback() {
        $disabled = ( isset( $this->options['briefinglab-jsondata-cms-entire-website'] ) && ( 1 == $this->options['briefinglab-jsondata-cms-entire-website'] ) ) ? 'disabled="disabled"' : '';

        $value = isset( $this->options['briefinglab-jsondata-cms-post-type'] ) ? esc_attr( $this->options['briefinglab-jsondata-cms-post-type']) : '';
        $selected_post_types = explode( '|||', $value );

        $post_types = get_post_types( array(), 'objects');

        unset($post_types['attachment']);
        unset($post_types['revision']);
        unset($post_types['nav_menu_item']);

        $format = '<br /><input type="checkbox" class="briefinglab-jsondata-cms-post-type" name="briefinglab-jsondata-cms-options[briefinglab-jsondata-cms-post-type][]" value="%s" %s %s/> %s';

        foreach( $post_types as $key => $value ){
            $checked = '';
            if( in_array( $key, $selected_post_types )) {
                $checked = 'checked="checked"';
            }

            printf( $format, $key, $checked, $disabled, $value->name );
        }

    }

    public function tab_list_callback() {
        $value = isset( $this->options['briefinglab-jsondata-cms-tab-list'] ) ? esc_attr( $this->options['briefinglab-jsondata-cms-tab-list']) : '';
        $description = '<p class="description">' . __('list the tabs to be showed splitted by ";" e.g: specifiche tecniche;descrizione;optionals', 'briefinglab-jsondata-cms') . '</p>';
        $format = '<br /><input type="text" class="large-text" id="briefinglab-jsondata-cms-tab-list" name="briefinglab-jsondata-cms-options[briefinglab-jsondata-cms-tab-list]" value="%s"/>%s';
        printf( $format, $value, $description);
    }


}