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
class SUMOHeavy_Postmark_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var null|SUMOHeavy_Postmark_Helper_Data
     */
    protected $tHelper = null;


    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->tHelper = Mage::helper('postmark');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('SUMOHeavy_Postmark_Helper_Data', $this->tHelper);
    }

    /**
     * @test
     * @loadFixture testIsEnabled
     */
    public function testIsEnabled()
    {
        $tIsEnabled = $this->tHelper->isEnabled();
        $this->assertEquals($this->expected('is-enabled')->getData('value'), $tIsEnabled);
    }

    /**
     * @test
     * @loadFixture testGetApiKey
     */
    public function testGetApiKey()
    {
        $tGetApiKey = $this->tHelper->getApiKey();
        $this->assertEquals($this->expected('api-key')->getData('value'), $tGetApiKey);
    }
}
