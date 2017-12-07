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

use bitExpert\ForceCustomerLogin\Controller\ModuleCheck;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ModuleCheckUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Controller
 */
class ModuleCheckUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function moduleEnabledSuccessfully()
    {
        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                ModuleCheck::MODULE_CONFIG_ENABLED,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn(true);

        $moduleCheck = new ModuleCheck($scopeConfig);

        $this->assertTrue($moduleCheck->isModuleEnabled());
    }
    /**
     * @test
     */
    public function moduleDisabledSuccessfully()
    {
        $scopeConfig = $this->getScopeConfig();
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                ModuleCheck::MODULE_CONFIG_ENABLED,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn(false);

        $moduleCheck = new ModuleCheck($scopeConfig);

        $this->assertFalse($moduleCheck->isModuleEnabled());
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
}
