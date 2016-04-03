<?php

namespace LengthOfRope\Treehouse\Template;

/**
 * Create an admin MetaBox using TAL
 *
 * Note: form data is saved as metadata if the post name equals the id,
 * so all data to be saved should have a input name like name='DemoBox[key]' if the id is DemoBox
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class MetaBox extends Template
{

    private $postTypes = array();
    private $identifier;
    private $context;
    private $priority;
    private $title;

    /**
     * Metabox constructor
     * 
     * @param string $identifier The unique ID for this metabox. Which is used for retrieving/ saving metadata.
     * @param string $templateFile The template file.
     * @param string $title The title of the Meta Box (should be translated)
     * @param array $postTypes The post types to limit the meta box to, empty array for all post types
     * @param string $context The position of the meta box (default, side, advanced)
     * @param string $priority The priority of the metabox (low, default, high)
     */
    public function __construct($identifier, $templateFile, $title, $postTypes = array(), $context = 'advanced', $priority = 'default')
    {
        parent::__construct($templateFile);

        $this->postTypes = $postTypes;
        $this->identifier = $identifier;
        $this->title = $title;
        $this->context = $context;
        $this->priority = $priority;

        add_action('add_meta_boxes', array($this, 'addMetaBoxAction'));
        add_action('save_post', array($this, 'savePostAction'));
    }

    /**
     * Called on action 'add_meta_boxes'.
     *
     * @access private
     * @param string $post_type
     */
    public function addMetaBoxAction($post_type)
    {
        // Only add the meta box if the correct post type
        if (count($this->postTypes) === 0 || in_array($post_type, $this->postTypes)) {
            wp_enqueue_media();
            add_meta_box(
                $this->identifier, $this->title, array($this, 'renderMetaBoxContent'), $post_type, $this->context, $this->priority
            );
        }
    }

    /**
     * Render the actual meta box
     *
     * @access private
     * @param object $post The current post
     */
    public function renderMetaBoxContent($post)
    {
        // Add a nonce field so we can check for it later.
        wp_nonce_field(strtolower($this->identifier) . '_metabox', strtolower($this->identifier) . '_metabox_nonce');

        // Get previously saved metadata for this box if any
        $data = get_post_meta($post->ID, '_metabox' . $this->identifier, true);

        if (isset($data) && is_array($data)) {
            $this->setTalData($data);
        }
        $this->echoExecute();
    }

    /**
     * This method is called on saving the post containing the MetaBox.
     *
     * @access private
     * @param integer $post_id
     */
    public function savePostAction($post_id)
    {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        $nonceCheck = strtolower($this->identifier) . '_metabox_nonce';
        if (!isset($_POST[$nonceCheck])) {
            return $post_id;
        }

        $nonce = $_POST[$nonceCheck];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, strtolower($this->identifier) . '_metabox')) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize the user input.
        $data = isset($_POST[$this->identifier]) ? $_POST[$this->identifier] : false;

        if (is_array($data)) {
            $this->sanatizeArray($data);

            // Update the meta data for this box
            update_post_meta($post_id, '_metabox' . $this->identifier, $data);
        }
    }

    private function sanatizeArray($data)
    {
        if (is_array($data)) {
            foreach ($data as &$value)
            {
                if (is_string($value)) {
                    $value = sanitize_text_field($value);
                }
                if (is_array($value)) {
                    $value = $this->sanatizeArray($value);
                }
            }
        }

        return $data;
    }

}
