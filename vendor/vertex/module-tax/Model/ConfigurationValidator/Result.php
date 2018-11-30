<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ConfigurationValidator;

use Vertex\Tax\Model\ConfigurationValidator;

/**
 * Contains the result of a Credential Check
 *
 * @see ConfigurationValidator
 */
class Result
{
    /** @var boolean */
    private $valid;

    /** @var string */
    private $message;

    /** @var array */
    private $arguments = [];

    /**
     * Get whether or not the credential check was valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Set whether or not the credential check was valid
     *
     * @param bool $valid
     * @return Result
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * Get the message associated with an invalid credential check
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the message associated with an invalid credential check
     *
     * @param string $message
     * @return Result
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get arguments for the message when ran through the localization layer
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set arguments for the message when ran through the localization layer
     *
     * @param array $arguments
     * @return Result
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }
}
