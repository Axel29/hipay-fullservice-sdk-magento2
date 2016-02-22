<?php
namespace HiPay\FullserviceMagento\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class HostedConfigProvider implements ConfigProviderInterface {
	
	/**
	 * @var string[]
	 */
	protected $methodCode = \HiPay\FullserviceMagento\Model\HostedMethod::HIPAY_METHOD_CODE;
	
	/**
	 * @var Checkmo
	 */
	protected $method;
	
	/**
	 * @var Escaper
	 */
	protected $escaper;
	
	/**
	 * Url Builder
	 *
	 * @var \Magento\Framework\Url
	 */
	protected $urlBuilder;
	
	/**
	 * @param PaymentHelper $paymentHelper
	 * @param Escaper $escaper
	 */
	public function __construct(
			PaymentHelper $paymentHelper,
			Escaper $escaper,
			\Magento\Framework\Url $urlBuilder
			) {
				$this->escaper = $escaper;
				$this->method = $paymentHelper->getMethodInstance($this->methodCode);
				$this->urlBuilder = $urlBuilder;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Magento\Checkout\Model\ConfigProviderInterface::getConfig()
	 */
	public function getConfig() {
		 return $this->method->isAvailable() ? [
            'payment' => [
                'hipayHosted' => [
                		'redirectUrl'=>$this->urlBuilder->getUrl('hipay/hosted/placeOrder',['_secure' => true]),
                ],
            ],
        ] : [];
	}
	

}