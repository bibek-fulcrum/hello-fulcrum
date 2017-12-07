<?php

namespace Redstage\Blog\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface BlogLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Redstage\Blog\Model\Blog
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
