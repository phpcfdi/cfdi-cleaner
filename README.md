# phpcfdi/cfdi-cleaner

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Discord][badge-discord]][discord]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Reliability][badge-reliability]][reliability]
[![Maintainability][badge-maintainability]][maintainability]
[![Code Coverage][badge-coverage]][coverage]
[![Violations][badge-violations]][violations]
[![Total Downloads][badge-downloads]][downloads]

> Herramienta para limpiar Comprobantes Fiscales Digitales por Internet mexicanos.

:us: The documentation of this project is in spanish as this is the natural language for the intended audience.

## Acerca de phpcfdi/cfdi-cleaner

Los archivos XML de Comprobantes Fiscales Digitales por Internet (CFDI) suelen contener errores.
Esta librería se encarga de reparar los errores (reparables) conocidos/comunes para poder trabajar con ellos.

Todas las operaciones que realiza esta librería con sobre partes del CFDI que no influyen en la generación
de la cadena de origen ni del sello.

## Instalación

Usa [composer](https://getcomposer.org/)

```shell
composer require phpcfdi/cfdi-cleaner
```

## Referencia de uso

La clase de trabajo es `\PhpCfdi\CfdiCleaner\Cleaner` y ofrece los siguientes métodos de limpieza:

### `staticClean(string $xml): string`

Realiza la limpieza de texto y del documento xml a partir de una cadena de caracteres
y entrega la representación limpia también en texto.

Este método es estático, por lo que no se necesita crear una instancia del objeto `Cleaner`.

```php
<?php
use PhpCfdi\CfdiCleaner\Cleaner;

$xml = file_get_contents('cfdi.xml');
echo Cleaner::staticClean($xml);
```

### `cleanStringToString(string $xml): string`

Realiza la limpieza de texto y del documento xml a partir de una cadena de caracteres
y entrega la representación limpia también en texto.

```php
<?php
use PhpCfdi\CfdiCleaner\Cleaner;

$xml = file_get_contents('cfdi.xml');
$cleaner = new Cleaner();
echo $cleaner->cleanStringToString($xml);
```

### `cleanStringToDocument(string $xml): DOMDocument`

Realiza la limpieza de texto y del documento xml a partir de una cadena de caracteres
y entrega el documento XML limpio.

Este método es útil si se necesita utilizar inmediatamente el objeto documento XML.

```php
<?php
use PhpCfdi\CfdiCleaner\Cleaner;

$xml = file_get_contents('cfdi.xml');
$cleaner = new Cleaner();
$document = $cleaner->cleanStringToDocument($xml);
echo $document->saveXML();
```

## Acciones de limpieza

Hay dos tipos de limpiezas que se pueden hacer, una sobre el texto XML antes de que se intente cargar como objetos DOM,
y la otra una vez que se pudo cargar el contenido como objetos DOM.

### Limpiezas sobre el texto XML

Estos limpiadores deben ejecutarse antes de intentar leer el contenido XML y están hechos para prevenir que el
objeto documento XML no se pueda crear.

#### `RemoveNonXmlStrings`

Elimina todo contenido antes del primer caracter `<` y posterior al último `>`.

#### `SplitXmlDeclarationFromDocument`

Separa por un `LF` (`"\n"`) la declaración XML `<?xml version="1.0"?>` del cuerpo XML.

#### `AppendXmlDeclaration`

Agrega `<?xml version="1.0"?>` al inicio del archivo si no existe, es muy útil porque
las herramientas de detección de `MIME` no reconocen un archivo XML si no trae la cabecera.

#### `XmlNsSchemaLocation`

Elimina un error frecuentemente encontrado en los CFDI emitidos por el SAT donde dice `xmlns:schemaLocation`
en lugar de `xsi:schemaLocation`. En caso de que existan ambos, el único que se mantiene es `xsi:schemaLocation`.

### Limpiezas sobre el documento XML (`DOMDocument`)

Estas limpiezas se realizan sobre el documento XML.

#### `RemoveAddenda`

Remueve cualquier nodo de tipo `Addenda` en el espacio de nombres `http://www.sat.gob.mx/cfd/3`.

#### `RemoveIncompleteSchemaLocations`

Actúa sobre cada uno de los `xsi:schemaLocations`.

Lee el contenido e intenta interpretar el espacio de nombres y la ubicación del esquema de validación.
Para considerar que es un esquema de validación verifica que termine con `.xsd` (insensible a mayúsculas o minusculas).
Si encuentra un espacio de nombres sin esquema lo omite.
Si encuentra un esquema sin espacio de nombres lo omite.

#### `RemoveNonSatNamespacesNodes`

Verifica todas las definiciones de espacios de nombres y si no pertenece a un espacio de nombres con la URI
`http://www.sat.gob.mx/**` entonces elimina los nodos y atributos relacionados.

#### `RemoveNonSatSchemaLocations`

Actúa sobre cada uno de los `xsi:schemaLocations`.

Verifica las definiciones de espacios de nombres y elimina todos los pares donde el espacio de nombres que no
correspondan a la URI `http://www.sat.gob.mx/**`.

#### `RemoveUnusedNamespaces`

Remueve todas las declaraciones de espacios de nombres (junto con su prefijo) que no estén en uso.

#### `RenameElementAddPrefix`

Agrega el prefijo al nodo que no lo tiene por estar utilizando la definición simple `xmlns`.
Además, elimina los namespace superfluos y las definiciones `xmlns` redundantes.

Ejemplo de CFDI sucio:

```xml
<cfdi:Comprobante xmlns="http://www.sat.gob.mx/cfd/4" xmlns:cfdi="http://www.sat.gob.mx/cfd/4">
  <Emisor xmlns="http://www.sat.gob.mx/cfd/4" />
  <cfdi:Receptor xmlns:cfdi="http://www.sat.gob.mx/cfd/4" />
</cfdi:Comprobante>
```

Ejemplo de CFDI limpio:

```xml
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4">
  <cfdi:Emisor />
  <cfdi:Receptor />
</cfdi:Comprobante>
```

#### `MoveNamespaceDeclarationToRoot`

Mueve todas las declaraciones de espacios de nombres al nodo raíz.

Por lo regular el SAT pide en la documentación técnica que los espacios de nombres se definan en el nodo raíz,
sin embargo, es frecuente que se definan en el nodo que los implementa.

Hay casos extremos de CFDI que siguen las reglas de XML, pero que no siguen las reglas de CFDI y generan prefijos
que se superponen. En este caso, se moverán solamente los espacios de nombres que no se superponen, por ejemplo:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
  <cfdi:Complemento>
    <cfdi:Otro xmlns:cfdi="http://www.sat.gob.mx/otro" />
    <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" />
  </cfdi:Complemento>
</cfdi:Comprobante>
```

Genera el siguiente resultado:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital">
  <cfdi:Complemento>
    <cfdi:Otro xmlns:cfdi="http://www.sat.gob.mx/otro" />
    <tfd:TimbreFiscalDigital />
  </cfdi:Complemento>
</cfdi:Comprobante>
```

Ante un caso como el anterior, no se están siguiendo las reglas establecidas en el Anexo 20 y en el complemento.
Es mejor que siempre considere ese caso como un CFDI inválido, aun cuando se haya firmado, y solicite la
sustitución por un CFDI que sí contenga los prefijos de los espacios de nombres correctos.

#### `MoveSchemaLocationsToRoot`

Mueve todas las declaraciones de ubicaciones de archivos de esquema al nodo principal.

Por lo regular el SAT pide en la documentación técnica que las ubicaciones de archivos de esquema se definan en
el nodo principal, sin embargo, es frecuente que se definan en el nodo que los implementa.

#### `SetKnownSchemaLocations`

Verifica que las ubicaciones de los esquemas de espacios de nombres conocidos sean exactamente las direcciones conocidas,
en caso de no serlo las modifican.

Anteriormente, el SAT permitía que las ubicaciones de los esquemas de espacios de nombres estuvieran escritos sin
sensibilidad a mayúsculas o minúsculas, incluso tenía varias ubicaciones para obtener estos archivos. Sin embargo,
recientemente ha eliminado la tolerancia a estas ubicaciones y solo permite las definiciones oficiales.

Este limpiador tiene la información de espacio de nombres, versión a la que aplica y ubicación conocida con base en
el proyecto [phpcfdi/sat-ns-registry](https://github.com/phpcfdi/sat-ns-registry).

En caso de que no se encuentre la ruta conocida para un espacio de nombres entonces no aplicará ninguna corrección
y dejará el valor tal como estaba.

#### `CollapseComplemento`

Este limpiador se crea para solventar la inconsistencia en la documentación del SAT.

Por un lado, en el Anexo 20 de CFDI 3.3, el SAT exige que exista uno y solamente un nodo `cfdi:Complemento`.
Sin embargo, en el archivo de validación XSD permite que existan más de uno.

Con esta limpieza, se deja un solo `cfdi:Complemento` con todos los complementos en él.

#### `RebuildDocument`

Este limpiador se crea para establecer los prefijos de espacios de nombres del SAT.

Lamentablemente, algunos PAC timbran (o han timbrado) sin seguir las instrucciones marcadas en la documentación
técnica del comprobante o de algún complemento, donde se establece el prefijo que se debe usar para el
espacio de nombres. Esto lleva a problemas de lectura.

Con esta limpieza, el archivo CFDI es leído y nodo por nodo se vuelve a construir, comprobando para cada atributo
y cada elemento hijo si corresponde a un espacio de nombres reservado por el SAT y establece el prefijo correcto.
Hay que tomar en cuenta que este limpiador puede llegar a consumir muchos recursos, debido a que mientras está
en ejecución cargará el archivo original en memoria y al recrearlo duplicará el consumo de la misma.

Los nodos que son recreados son: elementos, atributos, textos, *CDATA* y comentarios.

Ejemplo de archivo origen:

```xml
<x:Comprobante xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="http://www.sat.gob.mx/cfd/4"
  i:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
  >
  <x:Complemento>
    <t:TimbreFiscalDigital xmlns:t="http://www.sat.gob.mx/TimbreFiscalDigital" attr="foo-tfd" />
  </x:Complemento>
</x:Comprobante>
```

Después de hacer la limpieza:

```xml
<cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
  xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
  >
  <cfdi:Complemento>
    <cfdi:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" attr="foo-tfd" />
  </cfdi:Complemento>
</cfdi:Comprobante>
```

### Exclusión de limpiadores

Para no tener que modificar la creación del objeto limpiador y permitir la exclusión de limpiadores específicos,
y de esta forma ser compatibles con nuevas actualizaciones de la librería, se puede crear el limpiador estándar
y luego aplicar exclusiones.

El siguiente ejemplo muestra cómo excluir los limpiadores que afectan a una *Addenda*.

```php
<?php

use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiCleaner\ExcludeList;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveAddenda,
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveNonSatNamespacesNodes,
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveNonSatSchemaLocations,

/**
 * @var string $contents El contenido XML sucio.
 */

$exclude = new ExcludeList(
    RemoveAddenda::class,
    RemoveNonSatNamespacesNodes::class,
    RemoveNonSatSchemaLocations::class,
);

$cleaner = new Cleaner();
$cleaner->exclude($exclude);

$contents = $cleaner->cleanStringToString($contents);
```

## Soporte

Puedes obtener soporte abriendo un ticket en Github.

Adicionalmente, esta librería pertenece a la comunidad [PhpCfdi](https://www.phpcfdi.com/), así que puedes usar los
mismos canales de comunicación para obtener ayuda de algún miembro de la comunidad.

## Compatibilidad

Esta librería se mantendrá compatible con al menos la versión con
[soporte activo de PHP](https://www.php.net/supported-versions.php) más reciente.

También utilizamos [Versionado Semántico 2.0.0](docs/SEMVER.md) por lo que puedes usar esta librería
sin temor a romper tu aplicación.

## Contribuciones

Las contribuciones con bienvenidas. Por favor lee [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el archivo [CHANGELOG][].

## Copyright and License

The `phpcfdi/cfdi-cleaner` library is copyright © [PhpCfdi](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/cfdi-cleaner/blob/main/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/cfdi-cleaner/blob/main/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/cfdi-cleaner/blob/main/docs/TODO.md

[source]: https://github.com/phpcfdi/cfdi-cleaner
[php-version]: https://packagist.org/packages/phpcfdi/cfdi-cleaner
[discord]: https://discord.gg/aFGYXvX
[release]: https://github.com/phpcfdi/cfdi-cleaner/releases
[license]: https://github.com/phpcfdi/cfdi-cleaner/blob/main/LICENSE
[build]: https://github.com/phpcfdi/cfdi-cleaner/actions/workflows/build.yml?query=branch:main
[reliability]:https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-cleaner&metric=Reliability
[maintainability]: https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-cleaner&metric=Maintainability
[coverage]: https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-cleaner&metric=Coverage
[violations]: https://sonarcloud.io/project/issues?id=phpcfdi_cfdi-cleaner&resolved=false
[downloads]: https://packagist.org/packages/phpcfdi/cfdi-cleaner

[badge-source]: https://img.shields.io/badge/source-phpcfdi/cfdi--cleaner-blue?logo=github
[badge-discord]: https://img.shields.io/discord/459860554090283019?logo=discord
[badge-php-version]: https://img.shields.io/packagist/php-v/phpcfdi/cfdi-cleaner?logo=php
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfdi-cleaner?logo=git
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfdi-cleaner?logo=open-source-initiative
[badge-build]: https://img.shields.io/github/actions/workflow/status/phpcfdi/cfdi-cleaner/build.yml?branch=main&logo=github-actions
[badge-reliability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfdi-cleaner&metric=reliability_rating
[badge-maintainability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfdi-cleaner&metric=sqale_rating
[badge-coverage]: https://img.shields.io/sonar/coverage/phpcfdi_cfdi-cleaner/main?logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-violations]: https://img.shields.io/sonar/violations/phpcfdi_cfdi-cleaner/main?format=long&logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfdi-cleaner?logo=packagist
