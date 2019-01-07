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
class SUMOHeavy_Postmark_Model_Awrma_Email_Template extends AW_Rma_Model_Email_Template
{
    /**
     * If postmark is enabled and configured, force to send RMA emails through it.
     * This modificaton is necessary for aheadworksRMA module,
     * if you want to use Postmark integration to send RMA notification emails.
     */
    public function send($email, $name = null, array $variables = array())
    {
        $hlp = Mage::helper('postmark');
        if($hlp->isEnabled() && $hlp->getApiKey()) {
            $postmarkEmailTemplate = Mage::getModel('postmark/core_email_template');

            foreach($this->getData() as $key => $value) {
                $postmarkEmailTemplate->setData($key, $value);
            }

            $postmarkEmailTemplate->setTemplateFilter($this->getTemplateFilter());
            $postmarkEmailTemplate->setDesignConfig($this->getDesignConfig());

            return $postmarkEmailTemplate->send($email, $name, $variables);
        }

        return $this->callParentSend($email, $name, $variables);
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
