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

use bitExpert\ForceCustomerLogin\Helper\Strategy\StaticMatcher;

/**
 * Class StaticMatcherUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Helper\Strategy
 */
class StaticMatcherUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function matchStaticRulesCorrectly()
    {
        $matcher = new StaticMatcher('foobar');

        $this->assertEquals('foobar', $matcher->getName());

        /* @var $rule \bitExpert\ForceCustomerLogin\Model\WhitelistEntry */
        $rule = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $rule->expects($this->any())
            ->method('getUrlRule')
            ->willReturn('/foobar');

        /*
         * Rule: /foobar
         */
        // simple
        $this->assertTrue($matcher->isMatch('/foobar', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/', $rule));
        $this->assertFalse($matcher->isMatch('/bar', $rule));
        $this->assertFalse($matcher->isMatch('/bar/', $rule));
        // without rewrite
        $this->assertTrue($matcher->isMatch('/index.php/foobar', $rule));
        $this->assertTrue($matcher->isMatch('/index.php/foobar/', $rule));
        $this->assertFalse($matcher->isMatch('/index.php/bar', $rule));
        $this->assertFalse($matcher->isMatch('/index.php/bar/', $rule));
    }
}
