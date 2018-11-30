<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Logger\Handler;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Temando\Shipping\Webservice\Logger\LogAnonymizerInterface;

/**
 * Handler that logs to a separate file.
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class FileSystemHandler extends BaseHandler
{
    /**
     * @param DriverInterface $filesystem
     * @param LogAnonymizerInterface $anonymizer
     * @param string $filePath
     */
    public function __construct(
        DriverInterface $filesystem,
        LogAnonymizerInterface $anonymizer,
        $filePath = null
    ) {
        $this->fileName = '/var/log/temando.log';

        parent::__construct($filesystem, $filePath);

        $this->bubble = false;
        $this->pushProcessor($anonymizer);
    }
}
