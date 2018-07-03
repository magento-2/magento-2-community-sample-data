<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Config\Converter;

use Magento\Framework\Config\ConverterInterface;

/**
 * Converter class to manipulate XML into Array
 *
 * @package Klarna\Core\Config\Converter
 */
class Dom implements ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $klarnaNode = $this->getRootNode($source);
        $externalPaymentMethods = $this->getChildrenByName($klarnaNode, 'external_payment_method');
        $orderLines = $this->getChildrenByName($klarnaNode, 'order_lines');
        $paymentsOrderLines = $this->getChildrenByName($klarnaNode, 'payments_order_lines');
        return [
            'external_payment_methods' => $this->collectExternalPaymentMethods($externalPaymentMethods),
            'api_types'                => $this->collectChildren($klarnaNode, 'api_type'),
            'api_versions'             => $this->collectChildren($klarnaNode, 'api_version'),
            'merchant_checkbox'        => $this->collectChildren($klarnaNode, 'merchant_checkbox'),
            'order_lines'              => $this->collectOrderLines($orderLines),
            'payments_api_types'       => $this->collectChildren($klarnaNode, 'payments_api_type'),
            'payments_api_versions'    => $this->collectChildren($klarnaNode, 'payments_api_version'),
            'payments_order_lines'     => $this->collectOrderLines($paymentsOrderLines),
        ];
    }

    /**
     * Get root node from XML
     *
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    private function getRootNode(\DOMDocument $document)
    {
        $root = $this->getAllChildElements($document);
        return array_shift($root);
    }

    /**
     * Get all child elements from a node
     *
     * @param \DOMNode $source
     * @return \DOMElement[]
     */
    private function getAllChildElements(\DOMNode $source)
    {
        return array_filter(
            iterator_to_array($source->childNodes),
            function (\DOMNode $childNode) {
                return $childNode->nodeType === \XML_ELEMENT_NODE;
            }
        );
    }

    /**
     * Get all child elements from a named parent node
     *
     * @param \DOMElement $parent
     * @param string      $name
     * @param bool        $asArray
     * @return array|\DOMElement
     */
    private function getChildrenByName(\DOMElement $parent, $name, $asArray = true)
    {
        $element = array_filter(
            $this->getAllChildElements($parent),
            function (\DOMElement $child) use ($name) {
                return $child->nodeName === $name;
            }
        );
        if (!is_array($element)) {
            $element = [$element];
        }
        if ($asArray) {
            return $element;
        }
        return array_shift($element);
    }

    /**
     * Process external_payment_methods tree
     *
     * @param array $rootNode
     * @return array
     */
    private function collectExternalPaymentMethods(array $rootNode)
    {
        $result = [];
        foreach ($rootNode as $methodNode) {
            $method = $this->getAttribute($methodNode, 'id');
            $data = $this->collectAllChildValues($methodNode);
            if (array_key_exists($method, $result)) {
                $data = array_merge($result[$method], $data);
            }
            $result[$method] = $data;
        }
        return $result;
    }

    /**
     * Get an attribute from a node by attribute name
     *
     * @param \DOMElement $element
     * @param string      $name
     * @return string
     */
    private function getAttribute(\DOMElement $element, $name)
    {
        return $element->attributes->getNamedItem($name)->nodeValue;
    }

    /**
     * Iterate through tree from parent node building an
     * array of all elements and their values
     *
     * @param \DOMElement $parent
     * @return array
     */
    private function collectAllChildValues(\DOMElement $parent)
    {
        $childNodes = $this->getAllChildElements($parent);
        $data = [];
        foreach ($childNodes as $node) {
            $data[$node->nodeName] = [];
            foreach ($this->getChildrenByName($parent, $node->nodeName) as $childNode) {
                $result = $this->collectAllChildValues($childNode);
                $key = $node->nodeName;
                if ($node->hasAttribute('id')) {
                    $key = $node->getAttribute('id');
                    unset($data[$node->nodeName]);
                }
                $value = $node->nodeValue;
                if (!is_array($result)) {
                    $result = $value;
                }
                if (is_array($result) && empty($result)) {
                    $result = $value;
                }
                $data[$key] = $result;
            }
        }
        return $data;
    }

    /**
     * Process a tree from a node
     *
     * @param \DOMElement $rootNode
     * @param             $childName
     * @param string      $idField
     * @return array
     */
    private function collectChildren(\DOMElement $rootNode, $childName, $idField = 'id')
    {
        $result = [];
        foreach ($this->getChildrenByName($rootNode, $childName) as $methodNode) {
            $method = $this->getAttribute($methodNode, $idField);
            $data = $this->collectAllChildValues($methodNode);
            if (array_key_exists($method, $result)) {
                $data = array_merge($result[$method], $data);
            }
            $result[$method] = $data;
        }
        return $result;
    }

    /**
     * @param array $rootNode
     * @return array
     */
    private function collectOrderLines(array $rootNode)
    {
        $result = [];
        foreach ($rootNode as $lineNode) {
            $line = $this->getAttribute($lineNode, 'id');
            $lines = $this->getAllChildElements($lineNode);
            $data = $this->processLines($lines);
            if (array_key_exists($line, $result)) {
                $data = array_merge($result[$line], $data);
            }
            $result[$line] = $data;
        }
        return $result;
    }

    /**
     * @param \DOMElement[] $lines
     * @return array
     */
    private function processLines(array $lines)
    {
        $result = [];
        foreach ($lines as $line) {
            $result[$line->getAttribute('id')] = [
                'class' => $line->getAttribute('class')
            ];
        }
        return $result;
    }
}
