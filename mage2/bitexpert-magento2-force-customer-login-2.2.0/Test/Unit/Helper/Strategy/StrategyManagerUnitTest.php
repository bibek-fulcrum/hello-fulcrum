<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Test\Unit\Helper\Strategy;

use bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyManager;
use bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface;

/**
 * Class StrategyManagerUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Helper\Strategy
 */
class StrategyManagerUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getStrategyNamesSuccessfully()
    {
        /* @var $strategy1 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy1 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy1->expects($this->once())
            ->method('getName')
            ->willReturn('Static');
        /* @var $strategy2 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy2 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy2->expects($this->once())
            ->method('getName')
            ->willReturn('RegEx (All)');

        $manager = new StrategyManager([
            'default' => $strategy1,
            'regex-all' => $strategy2
        ]);

        $this->assertEquals(
            [
                'default' => 'Static',
                'regex-all' => 'RegEx (All)'
            ],
            $manager->getStrategyNames()
        );
    }

    /**
     * @test
     */
    public function getStrategyInstancesSuccessfully()
    {
        /* @var $strategy1 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy1 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy1->expects($this->once())
            ->method('getName')
            ->willReturn('Static');
        /* @var $strategy2 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy2 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy2->expects($this->once())
            ->method('getName')
            ->willReturn('RegEx (All)');

        $manager = new StrategyManager([
            'default' => $strategy1,
            'regex-all' => $strategy2
        ]);

        $this->assertEquals(
            [
                'default' => $strategy1,
                'regex-all' => $strategy2
            ],
            $manager->getStrategies()
        );
    }

    /**
     * @test
     */
    public function getStrategyInstanceSuccessfully()
    {
        /* @var $strategy1 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy1 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy1->expects($this->once())
            ->method('getName')
            ->willReturn('Static');
        /* @var $strategy2 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy2 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy2->expects($this->once())
            ->method('getName')
            ->willReturn('RegEx (All)');

        $manager = new StrategyManager([
            'default' => $strategy1,
            'regex-all' => $strategy2
        ]);

        $this->assertFalse($manager->has('foo'));
        $this->assertTrue($manager->has('regex-all'));
        $this->assertEquals($strategy2, $manager->get('regex-all'));
    }

    /**
     * @test
     */
    public function getDefaultStrategyInstanceSuccessfully()
    {
        /* @var $strategy1 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy1 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy1->expects($this->once())
            ->method('getName')
            ->willReturn('Static');
        /* @var $strategy2 StrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
        $strategy2 = $this->createMock('\bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyInterface');
        $strategy2->expects($this->once())
            ->method('getName')
            ->willReturn('RegEx (All)');

        $manager = new StrategyManager([
            'default' => $strategy1,
            'regex-all' => $strategy2
        ]);

        $this->assertFalse($manager->has('foo'));
        $this->assertTrue($manager->has('default'));
        $this->assertEquals($strategy1, $manager->get('foo'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Could not load rule strategy with identifier "foo"
     */
    public function throwErrorOnMissingMatchinStrategyAndNoDefaultStrategy()
    {
        $manager = new StrategyManager([]);
        $manager->get('foo');
    }
}
