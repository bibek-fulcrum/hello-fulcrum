<?php

namespace Redstage\Blog\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class BlogLoader implements BlogLoaderInterface
{
    /**
     * @var \Redstage\Blog\Model\BlogFactory
     */
    protected $blogFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Redstage\Blog\Model\BlogFactory $blogFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Redstage\Blog\Model\BlogFactory $blogFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->blogFactory = $blogFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $blog = $this->blogFactory->create()->load($id);
        $this->registry->register('current_blog', $blog);
        return true;
    }
}
