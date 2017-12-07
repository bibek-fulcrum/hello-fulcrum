<?php

/*
 * This file is part of the Force Login module for Magento2.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bitExpert\ForceCustomerLogin\Controller;

use \bitExpert\ForceCustomerLogin\Api\Controller\LoginCheckInterface;
use \bitExpert\ForceCustomerLogin\Api\Repository\WhitelistRepositoryInterface;
use bitExpert\ForceCustomerLogin\Helper\Strategy\StrategyManager;
use \bitExpert\ForceCustomerLogin\Model\Session;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Class LoginCheck
 * @package bitExpert\ForceCustomerLogin\Controller
 */
class LoginCheck extends Action implements LoginCheckInterface
{
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var WhitelistRepositoryInterface
     */
    protected $whitelistRepository;
    /**
     * @var StrategyManager
     */
    protected $strategyManager;
    /**
     * @var ModuleCheck
     */
    protected $moduleCheck;
    /**
     * @var ResponseHttp
     */
    protected $response;

    /**
     * Creates a new {@link \bitExpert\ForceCustomerLogin\Controller\LoginCheck}.
     *
     * @param Context $context
     * @param Session $session
     * @param ScopeConfigInterface $scopeConfig
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param StrategyManager $strategyManager
     * @param ModuleCheck $moduleCheck
     * @param ResponseHttp $response
     */
    public function __construct(
        Context $context,
        Session $session,
        ScopeConfigInterface $scopeConfig,
        WhitelistRepositoryInterface $whitelistRepository,
        StrategyManager $strategyManager,
        ModuleCheck $moduleCheck,
        ResponseHttp $response
    ) {
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->whitelistRepository = $whitelistRepository;
        $this->strategyManager = $strategyManager;
        $this->moduleCheck = $moduleCheck;
        $this->response = $response;
        parent::__construct($context);
    }

    /**
     * Manages redirect
     */
    public function execute()
    {
        if ($this->moduleCheck->isModuleEnabled() === false) {
            return;
        }

        $url = $this->_url->getCurrentUrl();
        $path = \parse_url($url, PHP_URL_PATH);
        $targetUrl = $this->getTargetUrl();

        // current path is already pointing to target url, no redirect needed
        if ($targetUrl === $path) {
            return;
        }

        // check if current url is a match with one of the ignored urls
        foreach ($this->whitelistRepository->getCollection()->getItems() as $rule) {
            /** @var $rule \bitExpert\ForceCustomerLogin\Model\WhitelistEntry */
            $strategy = $this->strategyManager->get($rule->getStrategy());
            if ($strategy->isMatch($path, $rule)) {
                return;
            }
        }

        $this->session->setAfterLoginReferer($path);

        $this->response->setNoCacheHeaders();
        $this->response->setRedirect($this->getRedirectUrl($targetUrl));
        $this->response->sendResponse();
    }

    /**
     * @param string $targetUrl
     * @return string
     */
    protected function getRedirectUrl($targetUrl)
    {
        return \sprintf(
            '%s%s',
            $this->_url->getBaseUrl(),
            $targetUrl
        );
    }

    /**
     * @return string
     */
    protected function getTargetUrl()
    {
        return $this->scopeConfig->getValue(
            self::MODULE_CONFIG_TARGET,
            ScopeInterface::SCOPE_STORE
        );
    }
}
