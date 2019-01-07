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
class SUMOHeavy_Postmark_Test_Model_Core_Email_Template extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('SUMOHeavy_Postmark_Model_Core_Email_Template', Mage::getModel('core/email_template'));
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

        $mock = $this->getModelMockBuilder('core/email_template')
            ->setMethods(array('callParentSend'))
            ->getMock();
        $mock->expects($this->exactly(1))->method('callParentSend')->willReturn(true);

        $this->replaceByMock('helper', 'postmark', $helperMock);
        $this->replaceByMock('model', 'core/email_template', $mock);

        $template = Mage::getModel('core/email_template');

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
        $helperMock->expects($this->exactly(2))->method('getApiKey')->willReturn('test-api-key');

        $mock = $this->getModelMockBuilder('core/email_template')
            ->setMethods(array('isValidForSend'))
            ->getMock();
        $mock->expects($this->exactly(1))->method('isValidForSend')->willReturn(true);

        $this->replaceByMock('helper', 'postmark', $helperMock);
        $this->replaceByMock('model', 'core/email_template', $mock);


        $mailTransportMock = $this->getMockBuilder('SUMOHeavy_Mail_Transport_Postmark')
            ->setConstructorArgs(array('test-api-key'))
            ->setMethods(array('send'))
            ->getMock();
        $mailTransportMock->expects($this->exactly(1))->method('send')->willReturn(true);

        $template = Mage::getModel('core/email_template');
        $template->setMailTransport($mailTransportMock);

        $result = $template->send('test@example.com');
        $this->assertTrue($result);
    }
}
