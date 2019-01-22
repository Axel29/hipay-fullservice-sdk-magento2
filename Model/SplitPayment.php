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

namespace HiPay\FullserviceMagento\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use HiPay\Fullservice\Enum\Transaction\TransactionState;
use HiPay\Fullservice\Enum\Transaction\ECI;

/**
 * Hipay Split Payment data model
 *
 * @package HiPay\FullserviceMagento
 * @author Kassim Belghait <kassim@sirateck.com>
 * @copyright Copyright (c) 2016 - HiPay
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 Licence
 * @link https://github.com/hipay/hipay-fullservice-sdk-magento2
 *
 * @method \HiPay\FullserviceMagento\Model\ResourceModel\SplitPayment _getResource()
 * @method \HiPay\FullserviceMagento\Model\ResourceModel\SplitPayment getResource()
 * @method int getOrderId()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setOrderId(int $orderId)
 * @method int getRealOrderId()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setRealOrderId(int $realOrderId)
 * @method int getCustomerId()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setCustomerId(int $customerId)
 * @method int getProfileId()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setProfileId(int $profileId)
 * @method string getCardToken()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setCardToken(int $cardToken)
 * @method float getBaseGrandTotal()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setBaseGrandTotal(string $baseGrandTotal)
 * @method float getBaseCurrencyCode()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setBaseCurrencyCode(string $baseCurrencyCode)
 * @method float getAmountToPay()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setAmountToPay(string $amountToPay)
 * @method \DateTime getDateToPay()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setDateToPay(string $dateToPay)
 * @method string getMethodCode()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setMethodCode(int $methodCode)
 * @method int getAttempts()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setAttempts(int $attempts)
 * @method int getStatus()
 * @method \HiPay\FullserviceMagento\Model\SplitPayment setStatus(int $status)
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SplitPayment extends \Magento\Framework\Model\AbstractModel
{

    const SPLIT_PAYMENT_STATUS_PENDING = 'pending';
    const SPLIT_PAYMENT_STATUS_FAILED = 'failed';
    const SPLIT_PAYMENT_STATUS_COMPLETE = 'complete';

    /**
     * @var \HiPay\FullserviceMagento\Model\FullserviceMethod
     */
    protected $method;

    /**
     *
     * @var \Magento\Sales\Model\Order $_order
     */
    protected $_order;

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory $orderF
     */
    protected $orderF;

    /**
     *
     * @var \Magento\Checkout\Helper\Data $checkoutHelper
     */
    protected $checkoutHelper;

    /**
     *
     * @var PaymentHelper $paymentHelper
     */
    protected $paymentHelper;

    /**
     * SplitPayment constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param \Magento\Sales\Model\OrderFactory $orderF
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PaymentHelper $paymentHelper,
        \Magento\Sales\Model\OrderFactory $orderF,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->paymentHelper = $paymentHelper;
        $this->orderF = $orderF;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * Init resource model and id field
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('HiPay\FullserviceMagento\Model\ResourceModel\SplitPayment');
        $this->setIdFieldName('split_payment_id');
    }

    protected function getMethodInstance()
    {

        if ($this->method === null) {
            $this->method = $this->paymentHelper->getMethodInstance($this->getMethodCode());
        }

        return $this->method;
    }

    public function getOrder()
    {

        if ($this->_order === null) {
            $this->_order = $this->orderF->create();
            $this->_order->getResource()->load($this->_order, $this->getOrderId());

            //set custom data before call api
            $desc = sprintf(
                __("Order SPLIT #%s by %s"),
                $this->_order->getIncrementId(),
                $this->_order->getCustomerEmail()
            );
            $this->_order->setForcedDescription($desc);
            $this->_order->setForcedAmount($this->getAmountToPay());
            //added because if the same order_id TPP response "Max Attempts exceed!"
            $this->_order->setForcedOrderId($this->_order->getIncrementId() . "-split-" . $this->getId());
            $this->_order->setForcedEci(ECI::RECURRING_ECOMMERCE);
            $this->_order->setForcedOperation(
                \HiPay\FullserviceMagento\Model\System\Config\Source\PaymentActions::PAYMENT_ACTION_SALE
            );
            $this->_order->setForcedCardToken($this->getCardToken());
        }

        return $this->_order;
    }

    public function canPay()
    {
        return $this->getStatus() == self::SPLIT_PAYMENT_STATUS_FAILED
        || $this->getStatus() == self::SPLIT_PAYMENT_STATUS_PENDING;
    }

    public function pay()
    {

        if (!$this->canPay()) {
            throw new LocalizedException(__('This split payment is already paid!'));
        }

        if (!$this->getId()) {
            throw new LocalizedException(__('Split Payment not found!'));
        }

        try {
            //Call TPP api
            $op = $this->getMethodInstance()->getGatewayManager($this->getOrder())->requestNewOrder();
            $state = $op->getState();

            switch ($state) {
                case TransactionState::COMPLETED:
                case TransactionState::FORWARDING:
                case TransactionState::PENDING:
                    $this->setStatus(self::SPLIT_PAYMENT_STATUS_COMPLETE);
                    break;
                case TransactionState::DECLINED:
                case TransactionState::ERROR:
                default:
                    $this->setStatus(self::SPLIT_PAYMENT_STATUS_FAILED);
                    $this->sendErrorEmail();
                    break;
            }
        } catch (Exception $e) {
            $this->setStatus(self::SPLIT_PAYMENT_STATUS_FAILED);
            $this->sendErrorEmail();
        }
        $this->setAttempts($this->getAttempts() + 1);

        $this->getResource()->save($this);

        return $this;
    }

    public function sendErrorEmail()
    {
        $message = __("Error on request split Payment HIPAY. Split Payment Id: " . $this->getId());
        $this->checkoutHelper->sendPaymentFailedEmail($this->getOrder(), $message, __('Split Payment Hipay'));
    }
}
