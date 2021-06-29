# CHANGELOG

## SemVer 2.0

Utilizamos [Versionado Semántico 2.0.0](SEMVER.md).

## Cambios en la rama principal sin liberación de nueva versión.

Los cambios no liberados se integran a la rama principal, pero no requieren de la liberación de una nueva versión.

## Version 1.1.0

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
