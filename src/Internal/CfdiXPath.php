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
class CfdiXPath
{
    public const ALLOWED_NAMESPACES = [
        'http://www.sat.gob.mx/cfd/3',
        'http://www.sat.gob.mx/cfd/4',
    ];

    /** @var DOMXPath */
    private $xpath;

    public function __construct(DOMXPath $xpath)
    {
        $this->xpath = $xpath;
    }

    public static function createFromDocument(DOMDocument $document): self
    {
        $xpath = new DOMXPath($document);
        $rootNamespace = $document->documentElement->namespaceURI ?? '';
        if (! in_array($rootNamespace, self::ALLOWED_NAMESPACES)) {
            $rootNamespace = '';
        }
        $xpath->registerNamespace('cfdi', $rootNamespace);
        $xpath->registerNamespace('xsi', XmlConstants::NAMESPACE_XSI);
        return new self($xpath);
    }

    /**
     * @param string $xpathQuery
     * @return DOMNodeList<DOMElement>
     */
    public function queryElements(string $xpathQuery): DOMNodeList
    {
        /** @var DOMNodeList<DOMElement> $list PHPStan does not detect empry DOMNodeList subtype */
        $list = $this->xpath->query($xpathQuery, null, false) ?: new DOMNodeList();
        return $list;
    }

    /**
     * @param string $xpathQuery
     * @return DOMNodeList<DOMAttr>
     */
    public function queryAttributes(string $xpathQuery): DOMNodeList
    {
        /** @var DOMNodeList<DOMAttr> $list PHPStan does not detect empry DOMNodeList subtype */
        $list = $this->xpath->query($xpathQuery, null, false) ?: new DOMNodeList();
        return $list;
    }
}
