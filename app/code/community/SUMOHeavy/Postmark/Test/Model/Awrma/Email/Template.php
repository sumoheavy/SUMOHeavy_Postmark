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

if (!class_exists('AW_Rma_Model_Email_Template')) {
    class AW_Rma_Model_Email_Template
    {
        public function getData()
        {
            return array();
        }

        public function getTemplateFilter()
        {
            return new Varien_Filter_Template();
        }

        public function getDesignConfig()
        {
            return array();
        }
    }
}

class SUMOHeavy_Postmark_Test_Model_Awrma_Email_Template extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('SUMOHeavy_Postmark_Model_Awrma_Email_Template', Mage::getModel('awrma/email_template'));
    }

    /**
     * @test
     */
    public function testSendPostmarkDisabled()
    {
        $helperMock = $this->getMockBuilder('SMV_Postmark_Helper_Data')
           ->setMethods(array('isEnabled'))
           ->getMock();
        $helperMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $mock = $this->getModelMockBuilder('awrma/email_template')
            ->setMethods(array('callParentSend'))
            ->getMock();
        $mock->expects($this->exactly(1))->method('callParentSend')->willReturn(true);

        $this->replaceByMock('helper', 'postmark', $helperMock);
        $this->replaceByMock('model', 'awrma/email_template', $mock);

        $template = Mage::getModel('awrma/email_template');

        $result = $template->send('test@example.com');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testSend()
    {
        $helperMock = $this->getMockBuilder('SMV_Postmark_Helper_Data')
           ->setMethods(array('isEnabled', 'getApiKey'))
           ->getMock();
        $helperMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $helperMock->expects($this->exactly(1))->method('getApiKey')->willReturn('test-api-key');

        $mock = $this->getModelMockBuilder('postmark/core_email_template')
            ->setMethods(array('send'))
            ->getMock();
        $mock->expects($this->exactly(1))->method('send')->willReturn(true);

        $this->replaceByMock('helper', 'postmark', $helperMock);
        $this->replaceByMock('model', 'postmark/core_email_template', $mock);

        $template = Mage::getModel('awrma/email_template');

        $result = $template->send('test@example.com');
        $this->assertTrue($result);
    }
}
