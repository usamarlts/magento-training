<?php

declare(strict_types=1);

namespace RLTSquare\Unit3\Helper\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeInterface;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeInterface
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeInterface
    ) {
        $this->storeManager = $storeManager;
        $this->scopeInterface = $scopeInterface;
        parent::__construct($context);
    }

    /**
     * get the default store code for default website with default store group
     * @return StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }

    /**
     * get the config vaule for requested path and default store
     * @param $configPath
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($configPath): mixed
    {
        return $this->scopeInterface->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getCode()
        );
    }
}
