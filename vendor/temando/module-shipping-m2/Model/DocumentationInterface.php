<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Documentation Interface.
 *
 * The documentation data object represents one dispatch or completion document,
 * e.g. a shipping label.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface DocumentationInterface
{
    const DOCUMENTATION_ID = 'documentation_id';
    const NAME = 'name';
    const TYPE = 'type';
    const SIZE = 'size';
    const MIME_TYPE = 'mime_type';
    const URL = 'url';

    /**
     * @return string
     */
    public function getDocumentationId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getSize();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return string
     */
    public function getUrl();
}
