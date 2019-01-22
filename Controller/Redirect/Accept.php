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
namespace HiPay\FullserviceMagento\Controller\Redirect;

use HiPay\FullserviceMagento\Controller\Fullservice;

/**
 * Accept controller
 *
 * Used to redirect the customer when payment is accepted
 *
 * @package HiPay\FullserviceMagento
 * @author Kassim Belghait <kassim@sirateck.com>
 * @copyright Copyright (c) 2016 - HiPay
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 * @link https://github.com/hipay/hipay-fullservice-sdk-magento2
 */
class Accept extends Fullservice
{

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        //MO/TO case
        if ($this->getRequest()->getParam('is_moto', false)) {
            $this->_customerSession->setFromMoto(true);
            $this->_customerSession->setAccept(true);
            return $this->resultRedirectFactory->create()->setPath('customer/account');
        }
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }
}
