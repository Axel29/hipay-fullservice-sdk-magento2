<?php
/**
 * HiPay Fullservice Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Apache 2.0 Licence
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * @copyright      Copyright (c) 2016 - HiPay
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 *
 */
namespace HiPay\FullserviceMagento\Model\SecureVault;

use HiPay\Fullservice\HTTP\Configuration\ConfigurationInterface;
use HiPay\FullserviceMagento\Model\Request\Type\Factory as RequestFactory;
use HiPay\FullserviceMagento\Model\Config\Factory as ConfigFactory;
use HiPay\Fullservice\SecureVault\Client\SecureVaultClient;
use HiPay\Fullservice\HTTP\SimpleHTTPClient;
use HiPay\Fullservice\SecureVault\Request\GenerateTokenRequest;
use HiPay\Fullservice\SecureVault\Model\PaymentCardToken;

/**
 * SecureVault Manager
 *
 * HiPay Fullservice SDK is used by the manager
 * So, all secure vault api call are centralized here
 *
 * @package HiPay\FullserviceMagento
 * @author Kassim Belghait <kassim@sirateck.com>
 * @copyright Copyright (c) 2016 - HiPay
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 * @link https://github.com/hipay/hipay-fullservice-sdk-magento2
 */
class Manager
{

    /**
     *
     * @var \HiPay\Fullservice\SecureVault\Client\SecureVaultClient $_vault
     */
    protected $_vault;

    /**
     *
     * @var HiPay\FullserviceMagento\Model\Config $_config
     */
    protected $_config;

    /**
     *
     * @var ConfigFactory
     */
    protected $_configFactory;

    /**
     *
     * @var RequestFactory
     */
    protected $_requestFactory;

    public function __construct(
        RequestFactory $requestfactory,
        ConfigFactory $configFactory,
        $params = []
    ) {
        $this->_configFactory = $configFactory;
        $this->_requestFactory = $requestfactory;

        if (isset($params['methodCode'])) {
            $methodCode = $params['methodCode'];
            $storeId = $params['storeId'];
            $this->_config = $this->_configFactory->create([
                'params' => [
                    'methodCode' => $methodCode,
                    'storeId' => $storeId
                ]
            ]);
            $clientProvider = new SimpleHTTPClient($this->_config);
            $this->_vault = new SecureVaultClient($clientProvider);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('Method code is required.'));
        }
    }

    /**
     * @return \HiPay\Fullservice\HTTP\ClientProvider
     */
    public function getClientProvider()
    {
        return $this->_vault->getClientProvider();
    }

    /**
     * @return  ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->_config;
    }

    /**
     *
     * @param int $cardNumber
     * @param int $cardExpiryMonth
     * @param int $cardExpiryYear
     * @param string $cvc
     * @param string $cardHolder
     * @param bool $multiUse
     * @return PaymentCardToken $paymentCardToken
     */
    public function requestGenerateToken(
        $cardNumber,
        $cardExpiryMonth,
        $cardExpiryYear,
        $cvc = "",
        $cardHolder = "",
        $multiUse = false
    ) {
        $generateTokenRequest = new GenerateTokenRequest();
        $generateTokenRequest->card_number = $cardNumber;
        $generateTokenRequest->card_expiry_month = $cardExpiryMonth;
        $generateTokenRequest->card_expiry_year = $cardExpiryYear;
        $generateTokenRequest->card_holder = $cardHolder;
        $generateTokenRequest->cvc = $cvc;

        $generateTokenRequest->multi_use = (int)$multiUse;

        $paymentCardToken = $this->_vault->requestGenerateToken($generateTokenRequest);

        return $paymentCardToken;
    }
}
