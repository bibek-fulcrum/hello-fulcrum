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

use bitExpert\ForceCustomerLogin\Helper\Strategy\RegExAllMatcher;

/**
 * Class RegExAllMatcherUnitTest
 * @package bitExpert\ForceCustomerLogin\Test\Unit\Helper\Strategy
 */
class RegExAllMatcherUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function matchStaticRulesCorrectly()
    {
        $matcher = new RegExAllMatcher('foobar');

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
        $this->assertFalse($matcher->isMatch('', $rule));
        $this->assertFalse($matcher->isMatch('/', $rule));
        $this->assertTrue($matcher->isMatch('/foobar', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/baz', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/baz/', $rule));
        // without rewrite
        $this->assertFalse($matcher->isMatch('/index.php', $rule));
        $this->assertFalse($matcher->isMatch('/index.php/', $rule));
        $this->assertTrue($matcher->isMatch('/index.php/foobar', $rule));
        $this->assertTrue($matcher->isMatch('/index.php/foobar/', $rule));
        $this->assertTrue($matcher->isMatch('/index.php/foobar/baz', $rule));
        $this->assertTrue($matcher->isMatch('/index.php/foobar/baz/', $rule));
    }
    
    /**
     * @test
     */
    public function matchCatchAllRuleCorrectly()
    {
        $matcher = new RegExAllMatcher('foobar');

        /* @var $rule \bitExpert\ForceCustomerLogin\Model\WhitelistEntry */
        $rule = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $rule->expects($this->any())
            ->method('getUrlRule')
            ->willReturn('/?');

        /*
         * Rule: /?
         */
        // simple
        $this->assertTrue($matcher->isMatch('', $rule));
        $this->assertTrue($matcher->isMatch('/', $rule));
        // subpage
        $this->assertTrue($matcher->isMatch('/foobar/baz', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/baz/', $rule));
    }
    
    /**
     * @test
     */
    public function matchCatchAllWithLineEndIdentifierRuleCorrectly()
    {
        $matcher = new RegExAllMatcher('foobar');

        /* @var $rule \bitExpert\ForceCustomerLogin\Model\WhitelistEntry */
        $rule = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $rule->expects($this->any())
            ->method('getUrlRule')
            ->willReturn('/?$');

        /*
         * Rule: /?$
         */
        // simple
        $this->assertTrue($matcher->isMatch('', $rule));
        $this->assertTrue($matcher->isMatch('/', $rule));
        // subpage
        $this->assertTrue($matcher->isMatch('/foobar/baz', $rule));
        $this->assertTrue($matcher->isMatch('/foobar/baz/', $rule));
    }
    
    /**
     * @test
     */
    public function matchHomepageRuleCorrectly()
    {
        $matcher = new RegExAllMatcher('foobar');

        /* @var $rule \bitExpert\ForceCustomerLogin\Model\WhitelistEntry */
        $rule = $this->getMockBuilder('\bitExpert\ForceCustomerLogin\Model\WhitelistEntry')
            ->disableOriginalConstructor()
            ->getMock();
        $rule->expects($this->any())
            ->method('getUrlRule')
            ->willReturn('^/?$');

        /*
         * Rule: ^/?$
         */
        // simple
        $this->assertTrue($matcher->isMatch('', $rule));
        $this->assertTrue($matcher->isMatch('/', $rule));
        // subpage
        $this->assertFalse($matcher->isMatch('/foobar/baz', $rule));
        $this->assertFalse($matcher->isMatch('/foobar/baz/', $rule));
    }
}
