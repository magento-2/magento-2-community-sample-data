<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Shipment;

use Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\Signatory;

/**
 * Temando API Shipment Export Declaration Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ExportDeclaration
{
    /**
     * @var string
     */
    private $exportReason;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\Signatory
     */
    private $signatory;

    /**
     * @var string
     */
    private $exportCategory;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\ExportCodes
     */
    private $exportCodes;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    private $declaredValue;

    /**
     * @var string
     */
    private $incoterm;

    /**
     * @return string
     */
    public function getExportReason()
    {
        return $this->exportReason;
    }

    /**
     * @param string $exportReason
     */
    public function setExportReason($exportReason)
    {
        $this->exportReason = $exportReason;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\Signatory
     */
    public function getSignatory()
    {
        return $this->signatory;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\Signatory $signatory
     */
    public function setSignatory(Signatory $signatory)
    {
        $this->signatory = $signatory;
    }

    /**
     * @return string
     */
    public function getExportCategory()
    {
        return $this->exportCategory;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\ExportCodes
     */
    public function getExportCodes()
    {
        return $this->exportCodes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration\ExportCodes $exportCodes
     */
    public function setExportCodes($exportCodes)
    {
        $this->exportCodes = $exportCodes;
    }

    /**
     * @param string $exportCategory
     */
    public function setExportCategory($exportCategory)
    {
        $this->exportCategory = $exportCategory;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    public function getDeclaredValue()
    {
        return $this->declaredValue;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue $declaredValue
     */
    public function setDeclaredValue($declaredValue)
    {
        $this->declaredValue = $declaredValue;
    }

    /**
     * @return string
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * @param string $incoterm
     */
    public function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;
    }
}
