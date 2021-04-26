<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMAttr;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use PhpCfdi\CfdiCleaner\Internal\SchemaLocation;
use PhpCfdi\CfdiCleaner\Internal\XmlAttributeMethodsTrait;
use PhpCfdi\CfdiCleaner\Internal\XmlConstants;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class SetKnownSchemaLocations implements XmlDocumentCleanerInterface
{
    /**
     * List of known namespace # version xsd locations as key value map
     * @see https://github.com/phpcfdi/sat-ns-registry
     *
     * @var array<string, string>
     */
    private const KNOWN_NAMESPACES = [
        'http://www.sat.gob.mx/cfd/3#3.3'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
        'http://www.sat.gob.mx/cfd/3#3.2'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd',
        'http://www.sat.gob.mx/cfd/3#3.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv3.xsd',
        'http://www.sat.gob.mx/cfd/2#2.2'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv22.xsd',
        'http://www.sat.gob.mx/cfd/2#2.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv2.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/retencionpagov1.xsd',
        'http://www.sat.gob.mx/TimbreFiscalDigital#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigital.xsd',
        'http://www.sat.gob.mx/TimbreFiscalDigital#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd',
        'http://www.sat.gob.mx/ecb#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ecb/ecb.xsd',
        'http://www.sat.gob.mx/ecc#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ecc/ecc.xsd',
        'http://www.sat.gob.mx/EstadoDeCuentaCombustible#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/EstadoDeCuentaCombustible/ecc11.xsd',
        'http://www.sat.gob.mx/EstadoDeCuentaCombustible12#1.2'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/EstadoDeCuentaCombustible/ecc12.xsd',
        'http://www.sat.gob.mx/donat#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/donat/donat.xsd',
        'http://www.sat.gob.mx/donat#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/donat/donat11.xsd',
        'http://www.sat.gob.mx/divisas#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/divisas/divisas.xsd',
        'http://www.sat.gob.mx/implocal#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd',
        'http://www.sat.gob.mx/leyendasFiscales#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/leyendasFiscales/leyendasFisc.xsd',
        'http://www.sat.gob.mx/pfic#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/pfic/pfic.xsd',
        'http://www.sat.gob.mx/TuristaPasajeroExtranjero#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/TuristaPasajeroExtranjero/TuristaPasajeroExtranjero.xsd',
        'http://www.sat.gob.mx/spei#'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/spei/spei.xsd',
        'http://www.sat.gob.mx/detallista#'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/detallista/detallista.xsd',
        'http://www.sat.gob.mx/ registrofiscal#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/cfdiregistrofiscal/cfdiregistrofiscal.xsd',
        'http://www.sat.gob.mx/nomina#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina11.xsd',
        'http://www.sat.gob.mx/nomina12#1.2'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina12.xsd',
        'http://www.sat.gob.mx/pagoenespecie#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/pagoenespecie/pagoenespecie.xsd',
        'http://www.sat.gob.mx/valesdedespensa#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/valesdedespensa/valesdedespensa.xsd',
        'http://www.sat.gob.mx/ConsumoDeCombustibles11#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/consumodecombustibles/consumodeCombustibles11.xsd',
        'http://www.sat.gob.mx/consumodecombustibles#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/consumodecombustibles/consumodecombustibles.xsd',
        'http://www.sat.gob.mx/aerolineas#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/aerolineas/aerolineas.xsd',
        'http://www.sat.gob.mx/notariospublicos#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/notariospublicos/notariospublicos.xsd',
        'http://www.sat.gob.mx/vehiculousado#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/vehiculousado/vehiculousado.xsd',
        'http://www.sat.gob.mx/servicioparcialconstruccion#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/servicioparcialconstruccion/servicioparcialconstruccion.xsd',
        'http://www.sat.gob.mx/renovacionysustitucionvehiculos#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/'
            . 'cfd/renovacionysustitucionvehiculos/renovacionysustitucionvehiculos.xsd',
        'http://www.sat.gob.mx/certificadodestruccion#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/certificadodestruccion/certificadodedestruccion.xsd',
        'http://www.sat.gob.mx/arteantiguedades#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/arteantiguedades/obrasarteantiguedades.xsd',
        'http://www.sat.gob.mx/ine#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ine/ine11.xsd',
        'http://www.sat.gob.mx/ine#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ine/ine10.xsd',
        'http://www.sat.gob.mx/ComercioExterior11#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior11/ComercioExterior11.xsd',
        'http://www.sat.gob.mx/ComercioExterior#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior/ComercioExterior10.xsd',
        'http://www.sat.gob.mx/Pagos#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd',
        'http://www.sat.gob.mx/GastosHidrocarburos10#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/GastosHidrocarburos10/GastosHidrocarburos10.xsd',
        'http://www.sat.gob.mx/iedu#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/iedu/iedu.xsd',
        'http://www.sat.gob.mx/ventavehiculos#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ventavehiculos/ventavehiculos11.xsd',
        'http://www.sat.gob.mx/ventavehiculos#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/ventavehiculos/ventavehiculos.xsd',
        'http://www.sat.gob.mx/terceros#1.1'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/terceros/terceros11.xsd',
        'http://www.sat.gob.mx/acreditamiento#1.0'
        => 'http://www.sat.gob.mx/sitio_internet/cfd/acreditamiento/AcreditamientoIEPS10.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/arrendamientoenfideicomiso#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/arrendamientoenfideicomiso/arrendamientoenfideicomiso.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos/dividendos.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/enajenaciondeacciones#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/pagosaextranjeros/pagosaextranjeros.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/fideicomisonoempresarial#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/fideicomisonoempresarial/fideicomisonoempresarial.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/intereses#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/intereses/intereses.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/intereseshipotecarios#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/intereseshipotecarios/intereseshipotecarios.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/operacionesconderivados#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/operacionesconderivados/operacionesconderivados.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro11#1.1'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro11/planesderetiro11.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/planesderetiro/planesderetiro.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/premios#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/premios/premios.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/sectorfinanciero#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/sectorfinanciero/sectorfinanciero.xsd',
        'http://www.sat.gob.mx/esquemas/retencionpago/1/PlataformasTecnologicas10#1.0'
        => 'http://www.sat.gob.mx/esquemas/retencionpago/1/'
            . 'PlataformasTecnologicas10/ServiciosPlataformasTecnologicas10.xsd',
    ];

    use XmlNamespaceMethodsTrait;
    use XmlAttributeMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('xs', XmlConstants::NAMESPACE_XSI);
        /** @var DOMNodeList<DOMAttr> $schemaLocationAttributes */
        $schemaLocationAttributes = $xpath->query('//@xs:schemaLocation', null, false);

        foreach ($schemaLocationAttributes as $schemaLocationAttribute) {
            $this->cleanNodeAttribute($document, $schemaLocationAttribute);
        }
    }

    private function cleanNodeAttribute(DOMDocument $document, DOMAttr $attribute): void
    {
        $schemaLocation = SchemaLocation::createFromValue($attribute->nodeValue);
        foreach ($schemaLocation->getPairs() as $namespace => $location) {
            $version = $this->obtainVersionOfNamespace($document, $namespace);
            $location = $this->obtainLocationForNamespaceVersion($namespace, $version, $location);
            $schemaLocation->setPair($namespace, $location);
        }
        $attribute->nodeValue = $schemaLocation->asValue();
    }

    private function obtainVersionOfNamespace(DOMDocument $document, string $namespace): string
    {
        return $this->obtainAttributeValueFromFirstNodeOfNamespace($document, $namespace, 'Version')
            ?: $this->obtainAttributeValueFromFirstNodeOfNamespace($document, $namespace, 'version');
    }

    private function obtainAttributeValueFromFirstNodeOfNamespace(
        DOMDocument $document,
        string $namespace,
        string $attributeName
    ): string {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('q', $namespace);

        $nodes = $xpath->query("//q:*[@$attributeName]", null, false);
        if (false === $nodes || 0 === $nodes->length) {
            return '';
        }

        // @phpstan-ignore-next-line PHPStan is fine, it just report an impossible scenario considering the query
        return $nodes->item(0)->attributes->getNamedItem($attributeName)->nodeValue;
    }

    private function obtainLocationForNamespaceVersion(string $namespace, string $version, string $default): string
    {
        return self::KNOWN_NAMESPACES[$namespace . '#' . $version] ?? $default;
    }
}
