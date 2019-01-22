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
namespace HiPay\FullserviceMagento\Model\Request\PaymentMethod;

use HiPay\Fullservice\Gateway\Request\PaymentMethod\QiwiWalletPaymentMethod;

/**
 * Qiwi wallet Payment Method Request Object
 *
 * @package HiPay\FullserviceMagento
 * @author Kassim Belghait <kassim@sirateck.com>
 * @copyright Copyright (c) 2016 - HiPay
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 * @link https://github.com/hipay/hipay-fullservice-sdk-magento2
 */
class QiwiWallet extends AbstractPaymentMethod
{
    protected function mapRequest()
    {
        $qiwiWalletPaymentMethod = new QiwiWalletPaymentMethod();
        $qiwiWalletPaymentMethod->qiwiuser = $this->_order->getPayment()->getAdditionalInformation('qiwiuser');

        return $qiwiWalletPaymentMethod;
    }
}
