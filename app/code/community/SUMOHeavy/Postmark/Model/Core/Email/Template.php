<?php
/**
 * Postmark integration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@sumoheavy.com so we can send you a copy immediately.
 *
 * @category    SUMOHeavy
 * @package     SUMOHeavy_Postmark
 * @copyright   Copyright (c) 2012 SUMO Heavy Industries, LLC
 * @notice      The Postmark logo and name are trademarks of Wildbit, LLC
 * @license     http://www.opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class SUMOHeavy_Postmark_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
{
    protected $mailTransport = null;

    /**
     * Send mail to recipient
     *
     * @param   array|string       $email        E-mail(s)
     * @param   array|string|null  $name         receiver name(s)
     * @param   array              $variables    template variables
     * @return  boolean
     **/
    public function send($email, $name = null, array $variables = array())
    {
        $hlp = Mage::helper('postmark');
        if (!$hlp->isEnabled() || !$hlp->getApiKey()) {
            return $this->callParentSend($email, $name, $variables);
        }

        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }

        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        $mail = $this->getMail();

        $mailTransport = $this->getMailTransport($hlp->getApiKey());
        Zend_Mail::setDefaultTransport($mailTransport);

        foreach ($emails as $key => $email) {
            $mail->addTo($email, $names[$key]);
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);

        if ($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject($this->getProcessedTemplateSubject($variables));

        $isStoreEmail = false;
        $storeEmails = Mage::getStoreConfig('trans_email');
        foreach ($storeEmails as $email) {
            if ($email['email'] == $this->getSenderEmail()) {
                $isStoreEmail = true;
                break;
            }
        }

        if ($isStoreEmail) {
            $mail->setFrom($this->getSenderEmail(), $this->getSenderName());
        } else {
            $mail->clearReplyTo();

            $mail->setFrom(Mage::getStoreConfig('trans_email/ident_support/email'), $this->getSenderName());
            $mail->setReplyTo($this->getSenderEmail());
        }

        try {
            $mail->send();
            $this->_mail = null;
        } catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);
            return false;
        }

        return true;
    }

    public function getMailTransport($apiKey)
    {
        if($this->mailTransport === null) {
            $this->mailTransport = new SUMOHeavy_Mail_Transport_Postmark($apiKey);
        }
        return $this->mailTransport;
    }

    public function setMailTransport($mailTransport)
    {
        $this->mailTransport = $mailTransport;
        return $this;
    }

    /**
     * Call parent send methid
     *
     * @param   array|string       $email        E-mail(s)
     * @param   array|string|null  $name         receiver name(s)
     * @param   array              $variables    template variables
     * @return  boolean
     **/
    protected function callParentSend($email, $name = null, array $variables = array())
    {
        return parent::send($email, $name, $variables);
    }
}
