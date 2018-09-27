<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\DocumentationInterface;
use Temando\Shipping\Model\DocumentationInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Generic\Documentation;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DocumentationResponseMapper
{
    /**
     * @var DocumentationInterfaceFactory
     */
    private $documentationFactory;

    /**
     * DocumentationResponseMapper constructor.
     * @param DocumentationInterfaceFactory $documentationFactory
     */
    public function __construct(DocumentationInterfaceFactory $documentationFactory)
    {
        $this->documentationFactory = $documentationFactory;
    }

    /**
     * @param Documentation $apiDoc
     * @return DocumentationInterface
     */
    public function map(Documentation $apiDoc)
    {
        $documentation = $this->documentationFactory->create(['data' => [
            DocumentationInterface::DOCUMENTATION_ID => $apiDoc->getId(),
            DocumentationInterface::NAME => $apiDoc->getDescription(),
            DocumentationInterface::TYPE => $apiDoc->getType(),
            DocumentationInterface::SIZE => $apiDoc->getSize(),
            DocumentationInterface::MIME_TYPE => $apiDoc->getMimeType(),
            DocumentationInterface::URL => $apiDoc->getUrl(),
        ]]);

        return $documentation;
    }
}
