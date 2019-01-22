<?php
/**
 * HiPay Fullservice Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @copyright      Copyright (c) 2016 - HiPay
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace HiPay\FullserviceMagento\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use HiPay\FullserviceMagento\Model\Config;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * HiPay Plugin
 *
 * Override execute Method in \Magento\Sales\Model\Order\Payment\State\CaptureCommand
 *
 * Used to set custom state and status to the order
 *
 * @package HiPay\FullserviceMagento
 * @author Kassim Belghait <kassim@sirateck.com>
 * @copyright Copyright (c) 2016 - HiPay
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 * @link https://github.com/hipay/hipay-fullservice-sdk-magento2
 */
class CaptureCommandPlugin
{

    /**
     * Run HiPay capture command
     * Used to set custom status and state when order is captured
     *
     * @param SalesOrder\Payment\State\CaptureCommand $subject
     * @param callable $proceed
     * @param OrderPaymentInterface $payment
     * @param $amount
     * @param OrderInterface $order
     * @return \Magento\Framework\Phrase|string
     */
    public function aroundExecute(
        \Magento\Sales\Model\Order\Payment\State\CaptureCommand $subject,
        callable $proceed,
        OrderPaymentInterface $payment,
        $amount,
        OrderInterface $order
    ) {
        if (strpos($payment->getMethod(), 'hipay') !== false) {
            $status = Config::STATUS_CAPTURE_REQUESTED;

            //Change status to processing (default) if validation status is Capture Requested (117)
            if ((int)$payment->getMethodInstance()->getConfigData('hipay_status_validate_order') == 117) {
                $status = false;
            }

            $state = SalesOrder::STATE_PROCESSING;

            $formattedAmount = $order->getBaseCurrency()->formatTxt($amount);

            $message = __(
                'An amount of %1 will be captured after being approved at the payment gateway.',
                $formattedAmount
            );

            $this->setOrderStateAndStatus($order, $status, $state);

            //Set payment to pending, to not paid the invoice
            /** @see Magento\Sales\Model\Order\Payment\Operations\CaptureOperation */
            $payment->setIsTransactionPending(true);
        } else {
            $message = $proceed($payment, $amount, $order);
        }

        return $message;
    }

    /**
     * @param SalesOrder $order
     * @param string $status
     * @param string $state
     * @return void
     */
    protected function setOrderStateAndStatus(SalesOrder $order, $status, $state)
    {
        if (!$status) {
            $status = $order->getConfig()->getStateDefaultStatus($state);
        }

        $order->setState($state)->setStatus($status);
    }
}
