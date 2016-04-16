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
     * @param string $translationDomain The domain to use for translations
     * @param array $postTypes The post types to limit the meta box to, empty array for all post types
     * @param string $context The position of the meta box (default, side, advanced)
     * @param string $priority The priority of the metabox (low, default, high)
     */
    public function __construct($identifier, $templateFile, $title, $translationDomain = "default",
        $postTypes = array(), $context = 'advanced', $priority = 'default')
    {
        parent::__construct($templateFile, $translationDomain);

        $this->postTypes = $postTypes;
        $this->identifier = $identifier;
        $this->title = $title;
        $this->context = $context;
        $this->priority = $priority;

        add_action('add_meta_boxes', function($postType) {
            $this->addMetaBoxAction($postType);
        });
        add_action('save_post', function($postID) {
            $this->savePostAction($postID);
        });
    }

    /**
     * Called on action 'add_meta_boxes'.
     *
     * @param string $postType
     */
    private function addMetaBoxAction($postType)
    {
        // Only add the meta box if the correct post type
        if (count($this->postTypes) === 0 || in_array($postType, $this->postTypes)) {
            wp_enqueue_media();
            add_meta_box(
                $this->identifier, $this->title,
                function($post) {
                $this->renderMetaBoxContent($post);
            }, $postType, $this->context, $this->priority
            );
        }
    }

    /**
     * Render the actual meta box
     *
     * @param object $post The current post
     */
    private function renderMetaBoxContent($post)
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
     * @param integer $postID
     */
    private function savePostAction($postID)
    {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Does the nonce validate?
        if (!$this->nonceValidates()) {
            return $postID;
        }

        // If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $postID;
        }

        // Check the user's permissions.
        if (!$this->checkPermissions()) {
            return $postID;
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize the user input.
        $data = filter_input(INPUT_POST, $this->identifier, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (is_array($data)) {
            $this->sanatizeArray($data);

            // Update the meta data for this box
            update_post_meta($postID, '_metabox' . $this->identifier, $data);
        }
    }

    /**
     * Check if the nonce for this metabox validates.
     *
     * @return boolean
     */
    private function nonceValidates()
    {
        // Check if our nonce is set.
        $nonceCheck = strtolower($this->identifier) . '_metabox_nonce';
        $nonce = filter_input(INPUT_POST, $nonceCheck, FILTER_SANITIZE_STRING);
        if (!isset($nonce)) {
            return false;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, strtolower($this->identifier) . '_metabox')) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current admin user is allowed to edit the metabox data.
     *
     * @return boolean
     */
    private function checkPermissions()
    {
        $postType = filter_input(INPUT_POST, 'post_type', FILTER_SANITIZE_STRING);

        if ($postType === 'page' && !current_user_can('edit_page', $postID)) {
            return false;
        } else if ($postType !== 'page' && !current_user_can('edit_post', $postID)) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize the array WordPress style
     *
     * @param array $data
     * @return array with sanatized strings
     */
    private function sanatizeArray($data)
    {
        // Bail early if not an array
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = sanitize_text_field($value);
            }
            if (is_array($value)) {
                $value = $this->sanatizeArray($value);
            }
        }

        return $data;
    }

}
