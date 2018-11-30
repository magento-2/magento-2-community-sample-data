<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

/**
 * Temando API Error Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Error
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var object
     */
    private $links;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $detail;

    /**
     * @var object
     */
    private $source;

    /**
     * @var object
     */
    private $meta;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return object
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param object $links
     * @return void
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param string $detail
     * @return void
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return object
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param object $source
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return object
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param object $meta
     * @return void
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }
}
