<?php

if (!defined('ABSPATH')) {
    exit;
}

class GOOGLE_AWORDS_KEYWORD_PLANNER_ADMIN_API
{

    /**
     * Constructor function
     */
    public function __construct()
    {
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 1);
    }

    /**
     * Generate HTML for displaying fields
     * @param  array   $field Field data
     * @param  boolean $echo  Whether to echo the field HTML or return it
     * @return void
     */

    public function display_field($data = array(), $post = false, $echo = true)
    {
        // Get field info
        if (isset($data['field'])) {
            $field = $data['field'];
        } else {
            $field = $data;
        }
        // Check for prefix on option name
        $option_name = '';
        if (isset($data['prefix'])) {
            $option_name = $data['prefix'];
        }
        // Get saved data
        $data = '';
        if ($post) {
            // Get saved field data
            $option_name .= $field['id'];
            $option = get_post_meta($post->ID, $field['id'], true);
            // Get data to display in field
            if (isset($option)) {
                $data = $option;
            }
        } else {
            // Get saved option
            $option_name .= $field['id'];
            $option = get_option($option_name);

            // Get data to display in field
            if (isset($option)) {
                $data = $option;
            }
        }

        // Show default data if no option saved and default is supplied
        if ($data === false && isset($field['default'])) {
            $data = $field['default'];
        } elseif ($data === false) {
            $data = '';
        }

        $html = '';

        switch ($field['type']) {

            case 'text':
            case 'url':
            case 'email':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '" />' . "\n <br>"  .  $field['description'];
                $html .= '<button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top" title="'.esc_attr($field['tooltip']).'">
                ?
              </button> ' ;
                break;

            case 'password':
            case 'number':
            case 'hidden':
                $min = '';
                if (isset($field['min'])) {
                    $min = ' min="' . esc_attr($field['min']) . '"';
                }

                $max = '';
                if (isset($field['max'])) {
                    $max = ' max="' . esc_attr($field['max']) . '"';
                }
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '"' . $min . '' . $max . '/>' . "\n";
                break;

            case 'text_secret':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value=""  />' . "\n";
                break;

            case 'textarea':
                //var_dump($field);exit;
                $html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" style="' . esc_attr(isset($field['style']) ? $field['style'] : '') . '">' . $data . '</textarea><br/>' . "\n";

                // wp_editor( $data, esc_attr($field['id']), esc_attr($option_name) );
                break;

            case 'checkbox':
                $checked = '';
                if ($data && 'on' == $data) {
                    $checked = 'checked="checked"';
                }
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
                break;
            case 'checkbox_multi':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if (in_array($k, (array) $data)) {
                        $checked = true;
                    }
                    $html .= '<p><label for="' . esc_attr($field['id'] . '_' . $k) . '" class="checkbox_multi"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label></p> ';
                }
                $html .= '<button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top" title="'.esc_attr($field['tooltip']).'">
                ?
              </button>';
                break;
                case 'radio':
                    foreach ($field['options'] as $k => $v) {
                        $checked = false;
                        if ($k == $data) {
                            $checked = true;
                        }
                        $html .= '<p><label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label></p> ';
                        // $html .= '<div title ="hello">?</div>';
                    }
                    $html .= '<button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top" title="'.esc_attr($field['tooltip']).'">
                    ?
                  </button>';
                    break;
            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if ($k == $data) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if (in_array($k, (array) $data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;
            case 'color':
?>
                <input id="color_code" class="color-picker" name="<?php
                                                                    esc_attr_e($option_name);
                                                                    ?>" type="text" value="<?php
                                                                                            esc_attr_e($data);
                                                                                            ?>" />
                <?php
                break;

            case 'button':
                $html .= '<button id="wooain_' . esc_attr($field['id']) . '" type="button" >' . esc_attr($field['placeholder']) . '</button><span id="wooain_tail_' . esc_attr($field['id']) . '"></span>' . "\n";
                break;
        }

        if (!$echo) {
            return $html;
        }

        echo $html;
    }

    /**
     * Validate form field
     * @param  string $data Submitted value
     * @param  string $type Type of field to validate
     * @return string       Validated value
     */
    public function validate_field($data = '', $type = 'text')
    {
        switch ($type) {
            case 'text':
                $data = sanitize_text_field($data);
                break;
            case 'url':
                $data = esc_url_raw($data);
                break;
            case 'email':
                $data = sanitize_email($data);
                break;
        }
        return $data;
    }

    /**
     * Add meta box to the dashboard
     * @param string $id            Unique ID for metabox
     * @param string $title         Display title of metabox
     * @param array  $post_types    Post types to which this metabox applies
     * @param string $context       Context in which to display this metabox ('advanced' or 'side')
     * @param string $priority      Priority of this metabox ('default', 'low' or 'high')
     * @param array  $callback_args Any axtra arguments that will be passed to the display function for this metabox
     * @return void
     */
    public function add_meta_box($id = '', $title = '', $post_types = array(), $context = 'advanced', $priority = 'default', $callback_args = null)
    {
        // Get post type(s)
        if (!is_array($post_types)) {
            $post_types = array($post_types);
        }
        // Generate each metabox
        foreach ($post_types as $post_type) {
            add_meta_box($id, $title, array($this, 'meta_box_content'), $post_type, $context, $priority, $callback_args);
        }
    }

    /**
     * Display metabox content
     * @param  object $post Post object
     * @param  array  $args Arguments unique to this metabox
     * @return void
     */
    public function meta_box_content($post, $args)
    {
        $fields = apply_filters($post->post_type . '_custom_fields', array(), $post->post_type);
        if (!is_array($fields) || 0 == count($fields)) {
            return;
        }
        echo '<div class="custom-field-panel">' . "\n";
        foreach ($fields as $field) {
            if (!isset($field['metabox'])) {
                continue;
            }
            if (!is_array($field['metabox'])) {
                $field['metabox'] = array($field['metabox']);
            }
            if (in_array($args['id'], $field['metabox'])) {
                $this->display_meta_box_field($field, $post);
            }
        }
        echo '</div>' . "\n";
    }

    /**
     * Dispay field in metabox
     * @param  array  $field Field data
     * @param  object $post  Post object
     * @return void
     */
    public function display_meta_box_field($field = array(), $post)
    {

        if (!is_array($field) || 0 == count($field)) {
            return;
        }

        $field = '<p class="form-field"><label for="' . $field['id'] . '">' . $field['label'] . '</label>' . $this->display_field($field, $post, false) . '</p>' . "\n";

        echo $field;
    }

    /**
     * Save metabox fields
     * @param  integer $post_id Post ID
     * @return void
     */
    public function save_meta_boxes($post_id = 0)
    {
        if (!$post_id) {
            return;
        }

        $post_type = get_post_type($post_id);

        $fields = apply_filters($post_type . '_custom_fields', array(), $post_type);

        if (!is_array($fields) || 0 == count($fields)) {
            return;
        }

        foreach ($fields as $field) {
            if (isset($_REQUEST[$field['id']])) {
                update_post_meta($post_id, $field['id'], $this->validate_field(sanitize_text_field($_REQUEST[$field['id']]), $field['type']));
            } else {
                update_post_meta($post_id, $field['id'], '');
            }
        }
    }
}
