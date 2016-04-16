<?php

namespace LengthOfRope\Treehouse\Template;

/**
 * Create an admin notice using TAL
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class AdminNotice extends Template
{

    const SUCCES = "notice-success";
    const INFO = "notice-info";
    const WARNING = "notice-warning";
    const ERROR = "notice-error";

    /**
     * Construct an Admin notification message.
     *
     * @param string $message The message to display to the user in the admin panel.
     * @param string $type The type of notice to display. Use one of the defined constants SUCCES, INFO, WARNING, ERROR
     * @param boolean $dismissable Set to true if the message is dismissable.
     */
    public function __construct($message, $type = self::SUCCES, $dismissable = false)
    {
        parent::__construct(TH_CORE_DIR . '/tpl/AdminNotice.xml', 'treehouse-core');

        $this->setTalData(array(
            'class' => 'notice ' . $type . ($dismissable ? ' is-dismissible' : ''),
            'message' => $message
        ));

        // Execute the template when needed by (requires PHP 5.4 but allows calling private method)
        add_action('admin_notices', function() {
            $this->addAdminNotice();
        });
    }

    /**
     * Called on action 'admin_notices'.
     *
     * @param string $postType
     */
    private function addAdminNotice()
    {
        $this->echoExecute();
    }

}
