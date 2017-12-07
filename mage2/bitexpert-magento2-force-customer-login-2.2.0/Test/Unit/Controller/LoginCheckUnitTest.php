<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Test\Unit\Controller;

/**
 * Class LoginCheckUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Controller
 */
class LoginCheckUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('\bitExpert\ForceCustomerLogin\Controller\LoginCheck'));
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function testConstructor()
    {
        $loginCheck = new \bitExpert\ForceCustomerLogin\Controller\LoginCheck(
            $this->getContext(),
            $this->getCustomerSession(),
            $this->getScopeConfig(),
            $this->getWhitelistRepository(),
            $this->getStrategyManager(),
            $this->getModuleCheck(),
            $this->getResponseHttp()
        );

        // check if mandatory interfaces are implemented
        $classInterfaces = class_implements($loginCheck);
        $this->assertContains('bitExpert\ForceCustomerLogin\Api\Controller\LoginCheckInterface', $classInterfaces);
    }

    /**
     * Run test with url equals target, so no redirecting is happening.
     * @test
     * @depends testConstructor
     */
    public function skipMatchingWhenModuleIsDisabled()
    {
        $moduleCheck = $this->getModuleCheck();
        $moduleCheck->expects($this->once())
            ->method('isModuleEnabled')
            ->willReturn(false);

        // --- Context
        $url = $this->getUrl();
        $url->expects($this->never())
            ->method('getCurrentUrl');

        $response = $this->getResponse();
        $redirect = $this->getRedirect();

        $context = $this->getContext();
        $context->expects($this->exactly(1))
            ->method('getUrl')
            ->will($this->returnValue($url));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($redirect));

        $loginCheck = new \bitExpert\ForceCustomerLogin\Controller\LoginCheck(
            $context,
            $this->getCustomerSession(),
            $this->getScopeConfig(),
            $this->getWhitelistRepository(),
            $this->getStrategyManager(),
            $moduleCheck,
            $this->getResponseHttp()
        );

        $loginCheck->execute();
    }

    /**
     * Run test with url equals target, so no redirecting is happening.
     * @test
     * @depends testConstructor
     */
    public function urlMatchesTargetUrlExactlyAndNoRedirectIsForced()
    {
        $urlString = 'http://example.tld/customer/account/login';
        $targetUrl = '/customer/account/login';

        // --- Scope Config
        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                \bitExpert\ForceCustomerLogin\Api\Controller\LoginCheckInterface::MODULE_CONFIG_TARGET,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->will($this->returnValue($targetUrl));

        // --- Context
        $url = $this->getUrl();
        $url->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($urlString));

        $response = $this->getResponse();
        $redirect = $this->getRedirect();

        $context = $this->getContext();
        $context->expects($this->exactly(1))
            ->method('getUrl')
            ->will($this->returnValue($url));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($redirect));

        // --- Response
        $responseHttp = $this->getResponseHttp();
        $responseHttp->expects($this->never())
            ->method('setNoCacheHeaders');
        $responseHttp->expects($this->never())
            ->method('setRedirect');
        $responseHttp->expects($this->never())
            ->method('sendResponse');

        // --- Whitelist Entries
        $whitelistRepository = $this->getWhitelistRepository();
        $whitelistRepository->expects($this->never())
            ->method('getCollection');

        // --- Strategy
        $strategyManager = $this->getStrategyManager();
        $strategyManager->expects($this->never())
            ->method('get');

        $loginCheck = new \bitExpert\ForceCustomerLogin\Controller\LoginCheck(
            $context,
            $this->getCustomerSession(),
            $scopeConfig,
            $whitelistRepository,
            $strategyManager,
            $this->getModuleCheck(),
            $responseHttp
        );

        $loginCheck->execute();
    }

    /**
     * Run test with data listed on the whitelist, so no redirecting is happening.
     * @test
     * @depends testConstructor
     */
    public function ruleMatchingPositiveWithoutRedirect()
    {
        $urlString = 'http://example.tld/foo/bar';
        $targetUrl = '/customer/account/login';

        // --- Scope Config
        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                \bitExpert\ForceCustomerLogin\Api\Controller\LoginCheckInterface::MODULE_CONFIG_TARGET,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->will($this->returnValue($targetUrl));

        // --- Context
        $url = $this->getUrl();
        $url->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($urlString));

        $response = $this->getResponse();
        $redirect = $this->getRedirect();

        $context = $this->getContext();
        $context->expects($this->exactly(1))
            ->method('getUrl')
            ->will($this->returnValue($url));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($redirect));

        // --- Response
        $responseHttp = $this->getResponseHttp();
        $responseHttp->expects($this->never())
            ->method('setNoCacheHeaders');
        $responseHttp->expects($this->never())
            ->method('setRedirect');
        $responseHttp->expects($this->never())
            ->method('sendResponse');

        // --- Whitelist Entries
        $whitelistEntityOne = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $whitelistEntityOne->expects($this->once())
            ->method('getStrategy')
            ->will($this->returnValue('default'));
        $whitelistCollection = $this
            ->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\ResourceModel\WhitelistEntry\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $whitelistCollection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$whitelistEntityOne]));
        $whitelistRepository = $this->getWhitelistRepository();
        $whitelistRepository->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($whitelistCollection));

        // --- Strategy
        $strategy = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy->expects($this->once())
            ->method('isMatch')
            ->with('/foo/bar', $whitelistEntityOne)
            ->willReturn(true);

        $strategyManager = $this->getStrategyManager();
        $strategyManager->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($strategy);

        $loginCheck = new \bitExpert\ForceCustomerLogin\Controller\LoginCheck(
            $context,
            $this->getCustomerSession(),
            $scopeConfig,
            $whitelistRepository,
            $strategyManager,
            $this->getModuleCheck(),
            $responseHttp
        );

        $loginCheck->execute();
    }

    /**
     * Run test with data not listed on the whitelist, so redirecting is forced.
     * @test
     * @depends testConstructor
     */
    public function ruleMatchingFailsAndResultsInRedirect()
    {
        $urlString = 'http://example.tld/foo/bar';
        $targetUrl = '/customer/account/login';

        // --- Scope Config
        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                \bitExpert\ForceCustomerLogin\Api\Controller\LoginCheckInterface::MODULE_CONFIG_TARGET,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->will($this->returnValue($targetUrl));

        // --- Context
        $url = $this->getUrl();
        $url->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($urlString));

        $response = $this->getResponse();
        $redirect = $this->getRedirect();

        $context = $this->getContext();
        $context->expects($this->exactly(1))
            ->method('getUrl')
            ->will($this->returnValue($url));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($redirect));

        // --- Response
        $responseHttp = $this->getResponseHttp();
        $responseHttp->expects($this->once())
            ->method('setNoCacheHeaders');
        $responseHttp->expects($this->once())
            ->method('setRedirect')
            ->with($targetUrl);
        $responseHttp->expects($this->once())
            ->method('sendResponse');

        // --- Whitelist Entries
        $whitelistEntityOne = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $whitelistEntityOne->expects($this->once())
            ->method('getStrategy')
            ->will($this->returnValue('default'));
        $whitelistCollection = $this
            ->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\ResourceModel\WhitelistEntry\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $whitelistCollection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$whitelistEntityOne]));
        $whitelistRepository = $this->getWhitelistRepository();
        $whitelistRepository->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($whitelistCollection));

        // --- Strategy
        $strategy = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy->expects($this->once())
            ->method('isMatch')
            ->with('/foo/bar', $whitelistEntityOne)
            ->willReturn(false);

        $strategyManager = $this->getStrategyManager();
        $strategyManager->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($strategy));

        $loginCheck = new \bitExpert\ForceCustomerLogin\Controller\LoginCheck(
            $context,
            $this->getCustomerSession(),
            $scopeConfig,
            $whitelistRepository,
            $strategyManager,
            $this->getModuleCheck(),
            $responseHttp
        );

        $loginCheck->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Action\Context
     */
    protected function getContext()
    {
        return $this->getMockBuilder('\Magento\Framework\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\bitExpert\ForceCustomerLogin\Model\Session
     */
    protected function getCustomerSession()
    {
        return $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\UrlInterface
     */
    protected function getUrl()
    {
        return $this->createMock('\Magento\Framework\UrlInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected function getScopeConfig()
    {
        return $this->getMockBuilder('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Response\RedirectInterface
     */
    protected function getRedirect()
    {
        return $this->createMock('\Magento\Framework\App\Response\RedirectInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ResponseInterface
     */
    protected function getResponse()
    {
        return $this->createMock('\Magento\Framework\App\ResponseInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyManager
     */
    protected function getStrategyManager()
    {
        return $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\bitExpert\ForceCustomerLogin\Controller\ModuleCheck
     */
    protected function getModuleCheck()
    {
        return $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Controller\ModuleCheck')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\bitExpert\ForceCustomerLogin\Api\Repository\WhitelistRepositoryInterface
     */
    protected function getWhitelistRepository()
    {
        return $this->createMock('\bitExpert\ForceCustomerLogin\Api\Repository\WhitelistRepositoryInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Response\Http
     */
    protected function getResponseHttp()
    {
        return $this->getMockBuilder('\Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
