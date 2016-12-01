<?php
namespace App\Module\Wechat;
use EasyWeChat\Payment;

/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 02/11/2016
 * Time: 18:20
 */
class MyPayment extends Payment\Payment
{
    protected $request;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Payment\Merchant $merchant
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Payment\Merchant $merchant, \Symfony\Component\HttpFoundation\Request $request = null)
    {
        $this->request = $request;
        parent::__construct($merchant);
    }

    /**
     * Return Notify instance.
     *
     * @return \EasyWeChat\Payment\Notify
     */
    public function getNotify()
    {
        if (empty($this->request)) {
            $notify =  new Payment\Notify($this->merchant);
        } else {
            $notify = new Payment\Notify($this->merchant, $this->request);
        }
        return $notify;
    }
}