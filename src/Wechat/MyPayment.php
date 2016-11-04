<?php

/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 02/11/2016
 * Time: 18:20
 */
class MyPayment extends \EasyWeChat\Payment\Payment
{
    protected $request;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Payment\Merchant $merchant
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\EasyWeChat\Payment\Merchant $merchant, \Symfony\Component\HttpFoundation\Request $request = null)
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
            return new \EasyWeChat\Payment\Notify($this->merchant);
        } else {
            return new \EasyWeChat\Payment\Notify($this->merchant, $this->request);
        }
    }
}