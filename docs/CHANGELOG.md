# CHANGELOG

## SemVer 2.0

Utilizamos [Versionado Semántico 2.0.0](SEMVER.md).

## Cambios en la rama principal sin liberación de nueva versión.

Los cambios no liberados se integran a la rama principal, pero no requieren de la liberación de una nueva versión.

### Integración continua

Se agrega PHP 8.1 a la matriz de pruebas.

## Versión 1.1.4

### Error al tratar espacios de nombres duplicados

Se encontraron casos en los que el CFDI firmado por un PAC tiene errores de espacios de nombres XML,
específicamente al duplicar un prefijo en uso en uno de los hijos. Si bien esto es correcto en XML,
no es correcto en un CFDI.

En este caso el limpiador `MoveNamespaceDeclarationToRoot` estaba generando una salida de XML no válida,
cambiando el prefijo, por ejemplo de `<cfdi:Complemento xmlns:cfdi="http://www.sat.gob.mx/cfd/3">`
a `<default:Complemento>`.

Se corrigió `MoveNamespaceDeclarationToRoot` para que utilice la misma estrategia alternativa de
espacios de nombres con prefijos sobrepuestos y entregue una salida correcta.

### Mantenimiento

- Se actualiza el año de licencia. ¡Feliz 2022!.
- Se corrigió el nombre de archivo de configuración de PHPStan y ahora usa el nombre correcto en `.gitattributes`,
de esta forma es correctamente excluido del paquete de distribución.
- Se cambia el flujo de integración continua de pasos en el trabajo a trabajos separados.
- Se corrige el nombre del grupo de mantenedores de código de PhpCfdi.
- Se cambia de `develop/install-development-tools` a `phive` para instalar las herramientas de desarrollo.

## Versión 1.1.3

### Error al tratar espacios de nombres predefinidos

Se encontraron casos en los que el CFDI firmado por un PAC tiene severos errores de espacios de nombres XML,
específicamente al redefinir un prefijo en uso por otro espacio de nombres. Si bien esto es correcto en XML,
no es correcto en un CFDI.

En este caso el limpiador `MoveNamespaceDeclarationToRoot` estaba generando una salida de XML no válida.

Se corrigió `MoveNamespaceDeclarationToRoot` para que utilice una estrategia alternativa en el caso de encontrar
espacios de nombres con prefijos sobrepuestos y entregue una salida correcta.

### `tests/clean.php`

Se agregó el archivo `tests/clean.php` para limpiar un archivo CFDI y entregar la respuesta en la salida estándar.

## Versión 1.1.2

Se encontró un error interno en el que, después de eliminar espacios de nombres no usados, se caía en un error
al momento de volver a iterar sobre los nodos de espacios de nombre. Lo que terminaba en una excepción.

Es importante actualizar si se está observando un error parecido a este:

```
TypeError: Argument 1 passed to PhpCfdi\CfdiCleaner\XmlDocumentCleaners\MoveNamespaceDeclarationToRoot::isNamespaceReserved()
must be of the type string, null given, called in .../vendor/phpcfdi/cfdi-cleaner/src/Internal/XmlNamespaceMethodsTrait.php on line 28
```

## Versión 1.1.1

En algunos casos, el limpiador de cadena de caracteres `RemoveNonXmlStrings` regresaba una cadena de caracteres vacía,
no pude determinar la causa exacta, pero fallaba con `preg_last_error_msg() == "JIT stack limit exhausted"`.

Este limpiador se encarga de eliminar cualquier caracter previo al primer `<` y posterior al último `>`.
Por lo que se ha cambiado a trabajo de cadenas de caracteres en lugar de expresiones regulares.

## Versión 1.1.0

Se agrega el limpiador de texto XML `SplitXmlDeclarationFromDocument` que separa la declaración XML del resto del
documento XML utilizando uno y solo un caracter `LF`. Por ejemplo:

```diff
--- <?xml version="1.0"?><root />
+++ <?xml version="1.0"?>
+++ <root />
```

Además, se incluyen los siguientes cambios previamente no liberados:

**2021-06-28**: Se reconfiguró PHPUnit para que fallara con un test incompleto o un *test suite* vacío,
pasara con un test riesgoso y no fuera *verbose*. 

**2021-06-28**: Se corrigió el título del código de conducta.

**2021-06-28**: Se corrigió el nombre de la prueba `AddXmlDeclarationTest` a `AppendXmlDeclarationTest`.

**2021-05-18**: Se reconfiguró el proyecto para el uso de `php-cs-fixer: ^3.0`.

**2021-05-18**: Se corrigieron las extensiones usadas por la acción `build.yml/setup-php`.

**2021-05-18**: Se actualiza la configuración de PHPUnit con la ubicación del caché.

**2021-04-28**: Las pruebas no funcionaban correctamente con `LibXML < 2.9.10`.
Presumiblemente por la canonicalización y recarga realizada por PHPUnit `sebastian/comparator`.
Esto provocaba que los test no pasaran en sistemas con estas versiones, por ejemplo, Scrutinizer.
La solución más simple fue cambiar los espacios de nombres `urn:foo` a `http://tempuri.org/foo`.

## Versión 1.0.0

- Versión inicial.
