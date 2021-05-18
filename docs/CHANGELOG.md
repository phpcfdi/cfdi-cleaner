# CHANGELOG

## SemVer 2.0

Utilizamos [Versionado Semántico 2.0.0](SEMVER.md).

## Cambios en la rama principal sin liberación de nueva versión.

Los cambios no liberados se integran a la rama principal, pero no requieren de la liberación de una nueva versión.

**2021-05-18**: Se reconfiguró el proyecto para el uso de `php-cs-fixer: ^3.0`.

**2021-05-18**: Se corrigieron las extensiones usadas por la acción `build.yml/setup-php`.

**2021-04-28**: Las pruebas no funcionaban correctamente con `LibXML < 2.9.10`.
Presumiblemente por la canonicalización y recarga realizada por PHPUnit `sebastian/comparator`.
Esto provocaba que los test no pasaran en sistemas con estas versiones, por ejemplo, Scrutinizer.
La solución más simple fue cambiar los espacios de nombres `urn:foo` a `http://tempuri.org/foo`.

## Versión 1.0.0

- Versión inicial.
