<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Kp\Api\Data\UrlsInterface;

/**
 * Class MerchantUrls
 *
 * @package Klarna\Kp\Model\Api\Request
 */
class MerchantUrls implements UrlsInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $confirmation;

    /**
     * @var string
     */
    private $push;

    /**
     * @var string
     */
    private $notification;

    /**
     * Constructor.
     *
     * @param string[] $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * URL of merchant confirmation page.
     *
     * @param string $confirmation
     */
    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * URL that will be requested when an order is completed. Should be different
     * than checkout and confirmation URLs. (max 2000 characters)
     *
     * @param string $push
     */
    public function setPush($push)
    {
        $this->push = $push;
    }

    /**
     * URL for notifications on pending orders. (max 2000 characters)
     *
     * @param string $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }
}
