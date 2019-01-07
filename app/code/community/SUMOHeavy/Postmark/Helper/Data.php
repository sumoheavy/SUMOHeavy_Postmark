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
class SUMOHeavy_Postmark_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'postmark/settings/enabled';
    const XML_PATH_API_KEY = 'postmark/settings/apikey';

    /**
     * Check in configuration if the integration can be used
     *
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Get Api key from configuration
     *
     * @param null $store
     * @return mixed
     */
    public function getApiKey($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_KEY, $store);
    }
}
