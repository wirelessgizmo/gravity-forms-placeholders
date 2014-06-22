<?php

/*
Plugin Name: Gravity Forms - Placeholder addon
Plugin URI: http://github/
Description: Adds a custom field to Gravity Form fields for placeholder support
Version: 1.0
Author: Dan Churchill
Author URI: https://github.com/wirelessgizmo
*/

class GravityFormsPlaceholder
{

    private $_pluginUrl = '';
    private $_fieldName = 'form_field_placeholder_value';

    /**
     * Register actions and filters
     */
    public function __construct()
    {

        $this->_pluginUrl = plugins_url(basename(dirname(__FILE__)));

        add_action("gform_field_advanced_settings", array($this, "admin_add_field_to_form"), 10, 2);
        add_action("gform_editor_js", array($this, "admin_editor_js"));
        add_action('gform_enqueue_scripts', array($this, "frontend_enqueue_placeholder"));

        add_filter("gform_tooltips", array($this, "admin_tooltip"));
        add_filter("gform_field_content", array($this, "frontend_field_content"), 10, 5);


    }


    /**
     * @param $position
     * @param $form_id
     *
     * Adds the actual field element to the Advanced tab
     *
     */
    public function admin_add_field_to_form($position, $form_id)
    {

        //show it just after the admin label
        if ($position == 50) {
            ?>
        <li class="placeholder_setting field_setting">
            <label for="field_placeholder_value">
                <?php _e("Placeholder", "gravityforms"); ?>
                <?php gform_tooltip($this->_fieldName) ?>
            </label>
            <input type="text" id="field_placeholder_value" size="35"
                   onkeyup="SetFieldProperty('<?php echo $this->_fieldName; ?>', this.value);"/>
        </li>
        <?php
        }

    }

    /**
     * Add which fields we support this field for and bind to populate the initial value
     */
    public function admin_editor_js()
    {
        ?>
    <script type='text/javascript'>
        //adding setting to fields of type "text"
        fieldSettings["text"] += ", .placeholder_setting";

        //binding to the load field settings event to initialize the checkbox
        jQuery(document).bind("gform_load_field_settings", function (event, field, form) {

            jQuery("#field_placeholder_value").val(field['<?php echo $this->_fieldName; ?>']);
        });
    </script>
    <?php
    }

    /**
     * @param $tooltips
     * @return mixed
     *
     * Add the tooltip when you hover over the item in the admin
     */
    public function admin_tooltip($tooltips)
    {
        $tooltips[$this->_fieldName] = "<h6>Placeholder</h6>Enter text here to appear as the items placeholder text";
        return $tooltips;
    }


    /**
     * Enqueue the fallback JS if it's required for non HTML5 support
     */
    public function frontend_enqueue_placeholder()
    {

        echo "<script>var jquery_placeholder_url = '" . $this->_pluginUrl . "/lib/jquery.placeholder-1.0.1.js';</script>";
        wp_enqueue_script('gf_placeholder_addon_js', $this->_pluginUrl . '/js/gf-placeholder.js', array('jquery'), '1.0');

    }

    /**
     * @param $content
     * @param $field
     * @param $value
     * @param $lead_id
     * @param $form_id
     * @return mixed
     *
     * Modify the content of a field to display placeholder
     */
    public function frontend_field_content($content, $field, $value, $lead_id, $form_id)
    {

        //if this field has the placeholder property defined add it as an option
        if (rgar($field, $this->_fieldName)) {

            $content = preg_replace('/<input /', '<input placeholder="' . $field[$this->_fieldName] . '" ', $content);

        }

        return $content;
    }
}

new GravityFormsPlaceholder();
