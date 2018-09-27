<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use \Magento\Framework\DataObject;

/**
 * Temando Shipment Export Declaration Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ExportDeclaration extends DataObject implements ExportDeclarationInterface
{
    /**
     * @return string
     */
    public function getSignatoryPersonTitle()
    {
        return $this->getData(ExportDeclarationInterface::SIGNATORY_PERSON_TITLE);
    }

    /**
     * @return string
     */
    public function getSignatoryPersonFirstName()
    {
        return $this->getData(ExportDeclarationInterface::SIGNATORY_PERSON_FIRST_NAME);
    }

    /**
     * @return string
     */
    public function getSignatoryPersonLastName()
    {
        return $this->getData(ExportDeclarationInterface::SIGNATORY_PERSON_LAST_NAME);
    }

    /**
     * @return string
     */
    public function getIncoterm()
    {
        return $this->getData(ExportDeclarationInterface::INCOTERM);
    }

    /**
     * @return string
     */
    public function getDeclaredValue()
    {
        return $this->getData(ExportDeclarationInterface::DECLARED_VALUE);
    }

    /**
     * @return string
     */
    public function getExportReason()
    {
        return $this->getData(ExportDeclarationInterface::EXPORT_REASON);
    }

    /**
     * @return string
     */
    public function getExportCategory()
    {
        return $this->getData(ExportDeclarationInterface::EXPORT_CATEGORY);
    }

    /**
     * @return string
     */
    public function getEdn()
    {
        return $this->getData(ExportDeclarationInterface::EDN);
    }

    /**
     * @return string
     */
    public function getEel()
    {
        return $this->getData(ExportDeclarationInterface::EEL);
    }

    /**
     * @return string
     */
    public function getEei()
    {
        return $this->getData(ExportDeclarationInterface::EEI);
    }

    /**
     * @return string
     */
    public function getItn()
    {
        return $this->getData(ExportDeclarationInterface::ITN);
    }

    /**
     * @return bool
     */
    public function isDutiable()
    {
        return $this->getData(ExportDeclarationInterface::IS_DUTIABLE);
    }
}
