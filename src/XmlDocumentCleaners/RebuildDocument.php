<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMAttr;
use DOMCdataSection;
use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

final class RebuildDocument implements XmlDocumentCleanerInterface
{
    /**
     * List of known namespace (key) and prefix (value)
     * @see https://github.com/phpcfdi/sat-ns-registry
     *
     * @var array<string, string>
     */
    private const NAMESPACE_PREFIX = [
        'http://www.w3.org/2001/XMLSchema-instance' => 'xsi',
        'http://www.sat.gob.mx/cfd/4' => 'cfdi',
        'http://www.sat.gob.mx/cfd/3' => 'cfdi',
        'http://www.sat.gob.mx/cfd/2' => 'cfd',
        'http://www.sat.gob.mx/esquemas/retencionpago/2' => 'retenciones',
        'http://www.sat.gob.mx/esquemas/retencionpago/1' => 'retenciones',
        'http://www.sat.gob.mx/TimbreFiscalDigital' => 'tfd',
        'http://www.sat.gob.mx/ecb' => 'ecb',
        'http://www.sat.gob.mx/ecc' => 'ecc',
        'http://www.sat.gob.mx/EstadoDeCuentaCombustible' => 'ecc11',
        'http://www.sat.gob.mx/EstadoDeCuentaCombustible12' => 'ecc12',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/enajenaciondeacciones' => 'enajenaciondeacciones',
        'http://www.sat.gob.mx/donat' => 'donat',
        'http://www.sat.gob.mx/divisas' => 'divisas',
        'http://www.sat.gob.mx/implocal' => 'implocal',
        'http://www.sat.gob.mx/leyendasFiscales' => 'leyendasFisc',
        'http://www.sat.gob.mx/pfic' => 'pfic',
        'http://www.sat.gob.mx/TuristaPasajeroExtranjero' => 'tpe',
        'http://www.sat.gob.mx/spei' => 'spei',
        'http://www.sat.gob.mx/detallista' => 'detallista',
        'http://www.sat.gob.mx/ registrofiscal' => 'registrofiscal',
        'http://www.sat.gob.mx/nomina' => 'nomina',
        'http://www.sat.gob.mx/nomina12' => 'nomina12',
        'http://www.sat.gob.mx/pagoenespecie' => 'pagoenespecie',
        'http://www.sat.gob.mx/valesdedespensa' => 'valesdedespensa',
        'http://www.sat.gob.mx/ConsumoDeCombustibles11' => 'consumodecombustibles11',
        'http://www.sat.gob.mx/consumodecombustibles' => 'consumodecombustibles',
        'http://www.sat.gob.mx/aerolineas' => 'aerolineas',
        'http://www.sat.gob.mx/notariospublicos' => 'notariospublicos',
        'http://www.sat.gob.mx/vehiculousado' => 'vehiculousado',
        'http://www.sat.gob.mx/servicioparcialconstruccion' => 'servicioparcial',
        'http://www.sat.gob.mx/renovacionysustitucionvehiculos' => 'decreto',
        'http://www.sat.gob.mx/certificadodestruccion' => 'destruccion',
        'http://www.sat.gob.mx/arteantiguedades' => 'obrasarte',
        'http://www.sat.gob.mx/ine' => 'ine',
        'http://www.sat.gob.mx/ComercioExterior20' => 'cce20',
        'http://www.sat.gob.mx/ComercioExterior11' => 'cce11',
        'http://www.sat.gob.mx/ComercioExterior' => 'cce',
        'http://www.sat.gob.mx/Pagos20' => 'pago20',
        'http://www.sat.gob.mx/Pagos' => 'pago10',
        'http://www.sat.gob.mx/GastosHidrocarburos10' => 'gceh',
        'http://www.sat.gob.mx/IngresosHidrocarburos10' => 'ieeh',
        'http://www.sat.gob.mx/CartaPorte' => 'cartaporte',
        'http://www.sat.gob.mx/CartaPorte20' => 'cartaporte20',
        'http://www.sat.gob.mx/CartaPorte30' => 'cartaporte30',
        'http://www.sat.gob.mx/CartaPorte31' => 'cartaporte31',
        'http://www.sat.gob.mx/iedu' => 'iedu',
        'http://www.sat.gob.mx/ventavehiculos' => 'ventavehiculos',
        'http://www.sat.gob.mx/terceros' => 'terceros',
        'http://www.sat.gob.mx/acreditamiento' => 'aieps',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/arrendamientoenfideicomiso' => 'arrendamientoenfideicomiso',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos' => 'dividendos',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/pagosaextranjeros' => 'pagosaextranjeros',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/fideicomisonoempresarial' => 'fideicomisonoempresarial',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/intereses' => 'intereses',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/intereseshipotecarios' => 'intereseshipotecarios',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/operacionesconderivados' => 'operacionesderivados',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro11' => 'planesderetiro11',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro' => 'planesderetiro',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/premios' => 'premios',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/sectorfinanciero' => 'sectorfinanciero',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/PlataformasTecnologicas10' => 'plataformasTecnologicas',
    ];

    /** @return array<string, string> */
    public static function getKnownNamespacePrefixEntries(): array
    {
        return self::NAMESPACE_PREFIX;
    }

    public function clean(DOMDocument $document): void
    {
        // do nothing if document does not have root element
        if (null === $document->documentElement) {
            return;
        }

        $created = new DOMDocument();
        $this->rebuildNode($created, $document->documentElement);
        $document->loadXML((string) $created->saveXML(), LIBXML_NSCLEAN | LIBXML_PARSEHUGE);
    }

    /**
     * @param DOMDocument|DOMElement $parent
     * @param DOMElement $source
     * @return void
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function rebuildNode($parent, DOMElement $source): void
    {
        /** @phpstan-var DOMDocument $document */
        $document = $parent instanceof DOMDocument ? $parent : $parent->ownerDocument;

        // Create new element
        $name = $this->obtainNameWithPrefix($source);
        /**
         * @var DOMElement $element
         */
        $element = $document->createElementNS($source->namespaceURI, $name);
        $parent->appendChild($element);

        // Add attributes (not included in childNodes)
        /** @var DOMAttr $attr */
        foreach ($source->attributes as $attr) {
            $element->setAttributeNS($attr->namespaceURI, $this->obtainNameWithPrefix($attr), $attr->value);
        }
        // Add children nodes
        foreach ($source->childNodes as $childNode) {
            // DOMCdataSection inheriths from DOMText
            if ($childNode instanceof DOMCdataSection) {
                $element->appendChild($document->createCDATASection((string) $childNode->nodeValue));
            } elseif ($childNode instanceof DOMText) {
                $element->appendChild($document->createTextNode((string) $childNode->nodeValue));
            }
            if ($childNode instanceof DOMComment) {
                $element->appendChild($document->createComment((string) $childNode->nodeValue));
            }
            if ($childNode instanceof DOMElement) {
                $this->rebuildNode($element, $childNode);
            }
        }
    }

    private function obtainNameWithPrefix(DOMNode $element): string
    {
        $prefix = self::NAMESPACE_PREFIX[(string) $element->namespaceURI] ?? (string) $element->prefix;
        if ('' === $prefix) {
            return (string) $element->localName;
        }

        return sprintf('%s:%s', $prefix, $element->localName);
    }
}
