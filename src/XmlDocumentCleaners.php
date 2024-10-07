<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

use DOMDocument;

class XmlDocumentCleaners implements XmlDocumentCleanerInterface
{
    /** @var XmlDocumentCleanerInterface[] */
    private $cleaners;

    public function __construct(XmlDocumentCleanerInterface ...$cleaners)
    {
        $this->cleaners = $cleaners;
    }

    public static function createDefault(): self
    {
        return new self(
            new XmlDocumentCleaners\RebuildDocument(),
            new XmlDocumentCleaners\RemoveAddenda(),
            new XmlDocumentCleaners\RemoveIncompleteSchemaLocations(),
            new XmlDocumentCleaners\RemoveNonSatNamespacesNodes(),
            new XmlDocumentCleaners\RemoveNonSatSchemaLocations(),
            new XmlDocumentCleaners\RemoveUnusedNamespaces(),
            new XmlDocumentCleaners\RenameElementAddPrefix(),
            new XmlDocumentCleaners\MoveNamespaceDeclarationToRoot(),
            new XmlDocumentCleaners\MoveSchemaLocationsToRoot(),
            new XmlDocumentCleaners\SetKnownSchemaLocations(),
            new XmlDocumentCleaners\CollapseComplemento(),
        );
    }

    public function clean(DOMDocument $document): void
    {
        foreach ($this->cleaners as $cleaner) {
            $cleaner->clean($document);
        }
    }

    public function withOutCleaners(ExcludeList $excludeList): self
    {
        $cleaners = $excludeList->filterObjects(...$this->cleaners);
        return new self(...$cleaners);
    }
}
