<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface AttachmentInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface AttachmentInterface extends ApiObjectInterface
{
    /**
     * The content type of the attachment.
     *
     * @param string $type
     */
    public function setContentType($type);

    /**
     * The body of the attachment in serialized JSON.
     *
     * @param string $body
     */
    public function setBody($body);
}
