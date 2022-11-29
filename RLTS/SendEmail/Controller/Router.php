<?php
declare(strict_types=1);

namespace RLTS\SendEmail\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use RLTS\SendEmail\Logger\Logger;

/**
 * Class Router
 */
class Router implements RouterInterface
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'email_configure_setting/general/custom_email';
    const FROM_EMAIL = 'email_configure_setting/general/email_from';
    const FROM_NAME = 'email_configure_setting/general/email_from_name';
    const TO_EMAIL = 'email_configure_setting/general/email_to';
    const TO_NAME = 'email_configure_setting/general/email_to_name';

    /**
     * @var ActionFactory
     */
    protected ActionFactory $actionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     */
    public function __construct(
        ActionFactory $actionFactory,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        Logger $logger
    ) {
        $this->actionFactory = $actionFactory;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        $identifier = trim($request->getPathInfo(), '/');

        if (str_contains($identifier, 'rltsquare')) {
            $request->setModuleName('rlts');
            $request->setControllerName('index');
            $request->setActionName('index');
            $this->logger->info("Page Visited");
            $this->sendEmail();
            return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class, ['request' => $request]);
        }
        return null;
    }

    public function sendEmail()
    {
        $senderAddress = $this->getConfigValue(self::FROM_EMAIL, $this->getStore()->getId());
        $senderName = $this->getConfigValue(self::FROM_NAME, $this->getStore()->getId());
        $receiverAddress = $this->getConfigValue(self::TO_EMAIL, $this->getStore()->getId());
        //$receiverName = $this->getConfigValue(self::TO_NAME, $this->getStore()->getId());
        $senderInfo = ['email' => $senderAddress, 'name' => $senderName];
        $templateId = $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE_FIELD, $this->getStore()->getId());

        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId(),
        ];
        $templateVars = [
            'store' => $this->storeManager->getStore(),
            'customer_name' => 'John David',
            'message' => 'Test Message'
        ];
        if (isset($senderAddress) && isset($receiverAddress)) {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope($senderInfo)
                ->addTo($receiverAddress)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->logger->info("Email Sended");
        } else {
            $this->logger->info("Email not Sended, Please Set the Email Sender and Email Receiver Address");
        }
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId): mixed
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore(): \Magento\Store\Api\Data\StoreInterface
    {
        return $this->storeManager->getStore();
    }
}
