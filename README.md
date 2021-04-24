# phpcfdi/cfdi-cleaner

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
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

#### `AppendXmlDeclaration`

Agrega `<?xml version="1.0"?>` al inicio del archivo si no existe, es muy útil porque
las herramientas de detección de `MIME` no reconocen un archivo XML si no trae la cabecera.

#### `XmlNsSchemaLocation`

Elimina un error frecuentemente encontrado en los CFDI emitidos por el SAT donde dice `xmlns:schemaLocation`
en lugar de `xsi:schemaLocation`.

#### `RemoveDuplicatedCfdi3Namespace`

Elimina la declaración del espacio de nombres de CFDI 3 sin prefijo `xmlns="http://www.sat.gob.mx/cfd/3"`
siempre y cuando también exista la declaración `xmlns:cfdi="http://www.sat.gob.mx/cfd/3"`.

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

Remueve todas las declaraciones de espacios de nombres cuando no correspondan a la URI `http://www.sat.gob.mx/**`,
por ejemplo `xmlns:foo="http://tempuri.org/foo"`.

#### `MoveNamespaceDeclarationToRoot`

Mueve todas las declaraciones de espacios de nombres al nodo raíz.

Por lo regular el SAT pide en la documentación técnica que los espacios de nombres se definan en el nodo raíz,
sin embargo es frecuente que se definan en el nodo que los implementa.

#### `MoveSchemaLocationsToRoot`

Mueve todas las declaraciones de ubicaciones de archivos de esquema al nodo principal.

Por lo regular el SAT pide en la documentación técnica que las ubicaciones de archivos de esquema se definan en
el nodo principal, sin embargo es frecuente que se definan en el nodo que los implementa.

#### `SetKnownSchemaLocations`

Verifica que las ubicaciones de los esquemas de espacios de nombres conocidos sean exactamente las direcciones conocidas,
en caso de no serlo las modifican.

Anteriormente el SAT permitía que las ubicaciones de los esquemas de espacios de nombres estuvieran escritos sin
sensibilidad a mayúsculas o minúsculas, incluso tenía varias ubicaciones para obtener estos archivos. Sin embargo,
recientemente ha eliminado la tolerancia a estas ubicaciones y solo permite las definiciones oficiales.

Este limpiador tiene la información de espacio de nombres, versión a la que aplica y ubicación conocida con base en
el proyecto [hpcfdi/sat-ns-registry](https://github.com/phpcfdi/sat-ns-registry).

En caso de que no se encuentre la ruta conocida para un espacio de nombres entonces no aplicará ninguna corrección
y dejará el valor tal como estaba.

#### `CollapseComplemento`

Este limpiador se crea para solventar la inconsistencia en la documentación del SAT.

Por un lado, en el Anexo 20 de CFDI 3.3, el SAT exige que exista uno y solamente un nodo `cfdi:Complemento`.
Sin embargo, en el archivo de validación XSD permite que existan más de uno.

Con esta limpieza, se deja un solo `cfdi:Complemento` con todos los complementos en él.

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
[release]: https://github.com/phpcfdi/cfdi-cleaner/releases
[license]: https://github.com/phpcfdi/cfdi-cleaner/blob/main/LICENSE
[build]: https://github.com/phpcfdi/cfdi-cleaner/actions/workflows/build.yml?query=branch:main
[quality]: https://scrutinizer-ci.com/g/phpcfdi/cfdi-cleaner/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/cfdi-cleaner/code-structure/main/code-coverage/src
[downloads]: https://packagist.org/packages/phpcfdi/cfdi-cleaner

[badge-source]: http://img.shields.io/badge/source-phpcfdi/cfdi--cleaner-blue?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfdi-cleaner?style=flat-square
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfdi-cleaner?style=flat-square
[badge-build]: https://img.shields.io/github/workflow/status/phpcfdi/cfdi-cleaner/build/main?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/cfdi-cleaner/main?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/cfdi-cleaner/main?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfdi-cleaner?style=flat-square
