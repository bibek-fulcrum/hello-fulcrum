<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Plugin;

use \Magento\Customer\Controller\Account\LoginPost;
use \Magento\Framework\Controller\Result\Redirect;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \bitExpert\ForceCustomerLogin\Model\Session;

/**
 * Class AfterLoginPlugin
 * @package bitExpert\ForceCustomerLogin\Plugin
 */
class AfterLoginPlugin
{
    /*
     * Redirect behaviour
     */
    const REDIRECT_DASHBOARD_ENABLED = '1';
    const REDIRECT_DASHBOARD_DISABLED = '0';
    /*
     * Configuration
     */
    const REDIRECT_DASHBOARD_CONFIG = 'customer/startup/redirect_dashboard';

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var string
     */
    protected $defaultTargetUrl;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * AfterLoginPlugin constructor.
     * @param Session $session
     * @param ScopeConfigInterface $scopeConfig
     * @param string $defaultTargetUrl
     */
    public function __construct(
        Session $session,
        ScopeConfigInterface $scopeConfig,
        $defaultTargetUrl
    ) {
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->defaultTargetUrl = $defaultTargetUrl;
    }

    /**
     * Customer login form page
     * @param LoginPost $customerAccountLoginController
     * @param Redirect $resultRedirect
     * @return Redirect
     */
    public function afterExecute(LoginPost $customerAccountLoginController, $resultRedirect)
    {
        if (self::REDIRECT_DASHBOARD_ENABLED ===
            $this->scopeConfig->getValue(self::REDIRECT_DASHBOARD_CONFIG)) {
            return $resultRedirect;
        }

        $targetUrl = $this->session->getAfterLoginReferer();
        if (empty($targetUrl)) {
            $targetUrl = $this->defaultTargetUrl;
        }

        /** @var $resultRedirect Redirect */
        $resultRedirect->setUrl($targetUrl);

        return $resultRedirect;
    }
}
