<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Internal;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

/**
 * @internal
 */
class Cfdi3XPath
{
    /** @var DOMXPath */
    private $xpath;

    public function __construct(DOMXPath $xpath)
    {
        $this->xpath = $xpath;
    }

    public static function createFromDocument(DOMDocument $document): self
    {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('cfdi', 'http://www.sat.gob.mx/cfd/3');
        $xpath->registerNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        return new self($xpath);
    }

    public function queryFirstElement(string $xpathQuery): ?DOMElement
    {
        $elements = $this->queryElements($xpathQuery);
        $element = $elements->item(0);
        if ($element instanceof DOMElement) {
            return $element;
        }
        return null;
    }

    /**
     * @param string $xpathQuery
     * @return DOMNodeList<DOMElement>
     */
    public function queryElements(string $xpathQuery): DOMNodeList
    {
        return $this->xpath->query($xpathQuery, null, false) ?: new DOMNodeList();
    }

    /**
     * @param string $xpathQuery
     * @return DOMNodeList<DOMAttr>
     */
    public function queryAttributes(string $xpathQuery): DOMNodeList
    {
        return $this->xpath->query($xpathQuery, null, false) ?: new DOMNodeList();
    }
}