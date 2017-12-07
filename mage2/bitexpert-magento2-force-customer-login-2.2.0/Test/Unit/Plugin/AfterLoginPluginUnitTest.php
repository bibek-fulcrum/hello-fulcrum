<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Test\Unit\Observer;

use bitExpert\ForceCustomerLogin\Plugin\AfterLoginPlugin;

/**
 * Class AfterLoginPluginUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Observer
 */
class AfterLoginPluginUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function runAfterExecuteWithRedirectDashboardOptionEnabled()
    {
        $session = $this->getSession();
        $session->expects($this->never())
            ->method('getAfterLoginReferer');

        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(AfterLoginPlugin::REDIRECT_DASHBOARD_CONFIG)
            ->willReturn(AfterLoginPlugin::REDIRECT_DASHBOARD_ENABLED);

        $plugin = new AfterLoginPlugin(
            $session,
            $scopeConfig,
            'default-target-url'
        );

        $loginPost = $this->getLoginPost();
        $redirect = $this->getRedirect();
        $redirect->expects($this->never())
            ->method('setUrl');

        $this->assertEquals($redirect, $plugin->afterExecute($loginPost, $redirect));
    }

    /**
     * @test
     */
    public function runAfterExecuteWithSpecificTargetUrl()
    {
        $session = $this->getSession();
        $session->expects($this->once())
            ->method('getAfterLoginReferer')
            ->willReturn('specific-target-url');

        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(AfterLoginPlugin::REDIRECT_DASHBOARD_CONFIG)
            ->willReturn(AfterLoginPlugin::REDIRECT_DASHBOARD_DISABLED);

        $plugin = new AfterLoginPlugin(
            $session,
            $scopeConfig,
            'default-target-url'
        );

        $loginPost = $this->getLoginPost();
        $redirect = $this->getRedirect();
        $redirect->expects($this->once())
            ->method('setUrl')
            ->with('specific-target-url');

        $this->assertEquals($redirect, $plugin->afterExecute($loginPost, $redirect));
    }

    /**
     * @test
     */
    public function runAfterExecuteWithDefaultTargetUrl()
    {
        $session = $this->getSession();
        $session->expects($this->once())
            ->method('getAfterLoginReferer')
            ->willReturn(null);

        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(AfterLoginPlugin::REDIRECT_DASHBOARD_CONFIG)
            ->willReturn(AfterLoginPlugin::REDIRECT_DASHBOARD_DISABLED);

        $plugin = new AfterLoginPlugin(
            $session,
            $scopeConfig,
            'default-target-url'
        );

        $loginPost = $this->getLoginPost();
        $redirect = $this->getRedirect();
        $redirect->expects($this->once())
            ->method('setUrl')
            ->with('default-target-url');

        $this->assertEquals($redirect, $plugin->afterExecute($loginPost, $redirect));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Controller\Account\LoginPost
     */
    private function getLoginPost()
    {
        return $this->getMockBuilder('\Magento\Customer\Controller\Account\LoginPost')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Controller\Result\Redirect
     */
    private function getRedirect()
    {
        return $this->getMockBuilder('\Magento\Framework\Controller\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\bitExpert\ForceCustomerLogin\Model\Session
     */
    private function getSession()
    {
        return $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods([
                'getAfterLoginReferer'
            ])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        return $this->getMockBuilder('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
