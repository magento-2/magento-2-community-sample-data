<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando ExportDeclaration Interface.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ExportDeclarationInterface
{
    const SIGNATORY_PERSON_TITLE = 'person_title';
    const SIGNATORY_PERSON_FIRST_NAME = 'person_first_name';
    const SIGNATORY_PERSON_LAST_NAME = 'person_last_name';
    const INCOTERM = 'incoterm';
    const DECLARED_VALUE = 'declared_value';
    const EXPORT_REASON = 'export_reason';
    const EXPORT_CATEGORY = 'export_category';
    const EDN = 'edn';
    const EEL = 'eel';
    const EEI = 'eei';
    const ITN = 'itn';
    const IS_DUTIABLE = 'isDutiable';

    /**
     * @return string
     */
    public function getSignatoryPersonTitle();

    /**
     * @return string
     */
    public function getSignatoryPersonFirstName();

    /**
     * @return string
     */
    public function getSignatoryPersonLastName();

    /**
     * @return string
     */
    public function getIncoterm();

    /**
     * @return string
     */
    public function getDeclaredValue();

    /**
     * @return string
     */
    public function getExportReason();

    /**
     * @return string
     */
    public function getExportCategory();

    /**
     * @return string
     */
    public function getEdn();

    /**
     * @return string
     */
    public function getEel();

    /**
     * @return string
     */
    public function getEei();

    /**
     * @return string
     */
    public function getItn();

    /**
     * @return string
     */
    public function isDutiable();
}
