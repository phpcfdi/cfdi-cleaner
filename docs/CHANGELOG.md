# CHANGELOG

## SemVer 2.0

Utilizamos [Versionado Semántico 2.0.0](SEMVER.md).

## Cambios en la rama principal sin liberación de nueva versión

Los cambios no liberados se integran a la rama principal, pero no requieren de la liberación de una nueva versión.

## Versión 1.4.0

Se agrega una nueva característica:

- Se agrega un nuevo limpiador `RebuildDocument` que reconstruye el documento a partir de uno original,
  especificando de forma correcta el prefijo de los espacios de nombres registrados en el SAT y el
  espacio de nombres `http://www.w3.org/2001/XMLSchema-instance`.
- Se asegura que la herramienta funciona en PHP 8.4.

Se hacen las siguientes correcciones:

- Se mejora el código interno de `RenameElementAddPrefix#cleanElement()` para una mejor comprensión,
  además de eliminar una comparación superflua. Gracias PHPStan. 
- Se hacen más correcciones menores para satisfacer el análisis de PHPStan.

Se hacen los siguientes cambios al entorno de desarrollo:

- Se agrega PHP 8.4 a la matrix de pruebas.
- Se ejecutan los trabajos de los flujos de trabajo de GitHub en PHP 8.4.
- Se actualiza la integración con *SonarQube Cloud*.
- Se actualizan las herramientas de desarrollo.

## Versión 1.3.4

Se hacen las siguientes correcciones:

- Se corrige la ubicación del XSD del complemento "Enajenaciones de acciones" para Retenciones e información de pagos.
- Se corrige la el espacio de nombres del complemento "Pagos a extranjeros" para Retenciones e información de pagos.
- El limpiador de *Addenda* incluye también los CFDI de Retenciones e información de pagos.

Se hacen los siguientes cambios al entorno de desarrollo:

- En el flujo de trabajo `build` en el trabajo `tests` se usa la variable `php-version` en singular.
- En el flujo de trabajo `coverage` en el trabajo `test-coverage` se usa mejora el título.
- Se actualizan las herramientas de desarrollo.

## Versión 1.3.3

- Se agrega *Complemento de Carta Porte 3.1* a la lista de espacio de nombres conocidos.

Se hacen los siguientes cambios al entorno de desarrollo:

- Se agrega a las herramientas de desarrollo `composer-normalize`:
  - Se agrega a los scripts de desarrollo de `composer` en `dev:check-style` y `dev:fix-style`.
  - Se agrega al flujo de trabajo de integración contínua.
  - Se normaliza el archivo `composer.json`.
- Se aplicó en los flujos de trabajo:
  - Se actualizan las acciones de GitHub a la versión 4.
  - Se permite la ejecución de los flujos de trabajo manualmente.
- Se excluye `test/_files` de la detección de lenguajes de GitHub.
- Se actualizan las herramientas de desarrollo.

## Versión 1.3.2

- Se agrega *Comercio Exterior 2.0* a la lista de espacio de nombres conocidos.
- Se actualiza el año de licencia.
- Se corrige la liga al proyecto en el archivo `CONTRIBUTING.md`.
- Se corrige el correo de comunicación en `CODE_OF_CONDUCT.md`.
- Se aplicó en los flujos de trabajo:
  - Se incluye PHP 8.3 a la matriz de pruebas.
  - Ejecutar todo en PHP 8.3.
- Se actualizan las herramientas de desarrollo.

## Versión 1.3.1

- Se agrega *Carta Porte 3.0* a la lista de espacio de nombres conocidos.

### Mantenimiento 2023-10-22

- Se corrige la configuración de *PHP-CS-Fixer*.
- Se corrigen las exclusiones de archivos para *SonarCloud*.
- Se actualizan las herramientas de desarrollo.

### Mantenimiento 2023-02-07

- Se refactoriza una prueba porque en PHPUnit 9.6.3 se deprecó el método `expectDeprecation()`.
- Se actualiza el año de la licencia. Feliz 2023.
- Se actualizan las herramientas de desarrollo.

## Versión 1.3.0

Se agrega la opción de excluir limpiadores específicos por nombre de clase.
En futuras versiones se implementará una mejor manera de manejar estas exclusiones.
La implementación actual no genera cambios que rompan la compatibilidad y requieran una versión mayor.

### Cambios de mantenimiento

- Se aplicó en los flujos de trabajo:
  - Incluir PHP 8.2 a la matriz de pruebas.
  - Ejecutar todo en PHP 8.2 excepto el trabajo `php-cs-fixer`.
  - Sustituir la instrucción `::set-output` con el uso del archivo `$GITHUB_OUTPUT`.
  - Se removió la restricción de versión fija de PHPStan.
- Se corrigió la insignia `badge-build`.
- Se actualizaron los archivos de estilo de código a las reglas utilizadas en los últimos proyectos.

## Versión 1.2.4

Se corrigen los limpiadores `RemoveAddenda` y `CollapseComplemento` porque no estaban actuando sobre CFDI 4.0.
Gracias `@luffynando`.

El problema de fondo es que la clase `Cfdi3XPath` solo actuaba sobre el XML namespace `http://www.sat.gob.mx/cfd/3` 
y nunca sobre `http://www.sat.gob.mx/cfd/4`. En la corrección se renombra la clase interna `Cfdi3XPath` a `CfdiXPath` 
y esta clase actúa sobre el XML namespace del nodo principal siempre que sea `http://www.sat.gob.mx/cfd/3` 
y `http://www.sat.gob.mx/cfd/4`.

Se refactoriza internamente la clase `CfdiXPath` y ahora incluye un método `querySchemaLocations`.

Se actualizan las librerías de desarrollo y el estilo de código. Siendo lo más importante la actualización de
PHPStan 1.7.15 que lleva a múltiples definiciones de tipos.

Se actualizan los flujos de trabajo de GitHub para usar PHP 8.1 y las acciones de GitHub en versión 3.

## Versión 1.2.3

La limpieza de CFDI grandes tardaba mucho tiempo en el limpiador `RemoveUnusedNamespaces`.
Se optimizó para que el resultado de la llamada al método privado `isPrefixedNamespaceOnUse` (*puro*) 
fuera almacenado en *caché* y así evitar hacer consultas XPath innecesarias.
Después de la optimización, la ejecución de limpieza en un CFDI con más de 2500 conceptos pasó de 
180 segundos a menos de 0.5 segundos.

## Versión 1.2.2

Se modifica el limpiador `XmlNsSchemaLocation` para que la limpieza se realice a nivel elemento XML.
Si no existe un atributo `xsi:schemaLocation` entonces el atributo `xmlns:schemaLocation` es renombrado.
Si ya existe un atributo `xsi:schemaLocation` entonces el atributo `xmlns:schemaLocation` es eliminado.
Esta modificación cierra el *issue* #13.

## Versión 1.2.1

Se agrega la definición del espacio de nombres de *Ingresos de Hidrocarburos 1.0* a `SetKnownSchemaLocations`.
Con esta actualización se corrige el proceso de integración continua.

Se corrige el estilo de código:

- Se modifican los textos HEREDOC usados como argumentos de funciones.
- Se actualiza `php-cs-fixer` de `3.6.0` a `3.8.0`.

## Versión 1.2.0

### Definición de XML namespace duplicado pero sin uso

Se han encontrado casos donde hay CFDI que incluyen un namespace que está en uso pero con un prefijo sin uso.

En el siguiente ejemplo, el espacio de nombres `http://www.sat.gob.mx/TimbreFiscalDigital` está declarado con el
prefijo `nsx` y `tfd`, donde el primer prefijo no está en uso y el segundo sí.

```xml
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
        xmlns:nsx="http://www.sat.gob.mx/TimbreFiscalDigital"
        xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital">
  <tfd:TimbreFiscalDigital UUID="X"/>
</cfdi:Comprobante>
```

Se ha modificado el limpiador `RemoveUnusedNamespaces` para que cuando detecta si un espacio de nombres está
en uso detecte también el prefijo. Con este cambio, el resultado de la limpieza sería:

```xml
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
        xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital">
  <tfd:TimbreFiscalDigital UUID="X"/>
</cfdi:Comprobante>
```

### Definición de XML namespace duplicado y sin prefijo

Se han encontrado casos donde hay CFDI *sucios*, pero válidos, donde la definición de los nodos
no cuenta con un prefijo. En estos casos el limpiador está produciendo un CFDI inválido después de limpiar.

Para corregir este problema:

- Se elimina de la lista de limpiadores de texto por defecto a `RemoveDuplicatedCfdi3Namespace`.
- Se quita la funcionalidad de `RemoveDuplicatedCfdi3Namespace` y se emite un `E_USER_DEPRECATED`.
- Se crea un nuevo limpiador `RenameElementAddPrefix` que agrega el prefijo al nodo que no lo tiene por estar
  utilizando la definición simple `xmlns`. Además, elimina los namespace superfluos y las 
  definiciones `xmlns` redundantes.

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

### El limpiador `RemoveDuplicatedCfdi3Namespace` ha sido deprecado

El limpiador `RemoveDuplicatedCfdi3Namespace` ha sido deprecado porque existen casos con un XML válido,
pero sucio, y el limpiador convierte el CFDI en inválido. La funcionalidad será absorvida por otro limpiador.

CFDI con XML correcto, pero sucio:

```xml
<cfdi:Comprobante xmlns="http://www.sat.gob.mx/cfd/3" xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
  <Emisor xmlns="http://www.sat.gob.mx/cfd/3" />
</cfdi:Comprobante>
```

Resultado del limpiador, donde `Emisor` ahora no pertenece al espacio de nombres `http://www.sat.gob.mx/cfd/3`.
El XML es correcto, pero como CFDI ya no lo es:

```xml
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
  <Emisor />
</cfdi:Comprobante>
```

### Mejoras al manejo interno de definiciones de espacios de nombres XML

Se modificó el *trait* `XmlNamespaceMethodsTrait` para que detectara si un elemento de espacios de nombres
`DOMNameSpaceNode` está eliminado revisando si la propiedad `namespaceURI` es `NULL`.
Antes se validaba contra la propiedad `nodeValue`, pero esta propiedad puede ser vacía, por ejemplo en `xmlns=""`.

Al momento de verificar si un espacio de nombres es reservado, ya no se excluye cuando el espacio de nombres es vacío.

### Eliminación de definición de espacio de nombres sin prefijo

Se modificó el *trait* `XmlNamespaceMethodsTrait` para que pueda eliminar un espacio de nombres sin prefijo,
por ejemplo `xmlns="http://tempuri.org/root"` o `xmlns=""`. 

## Versión 1.1.5

### Espacios de nombres conocidos

Se actualiza la lista de espacios de nombres conocidos para:

- CFDI 4.0.
- CFDI de retenciones e información de pagos 2.0.
- Complemento de pagos 2.0.
- Complemento de carta porte 1.0.
- Complemento de carta porte 2.0.

Además, se agrega una prueba que usa <https://github.com/phpcfdi/sat-ns-registry> para verificar que la lista
se mantiene actualizada.

### Integración continua

- Se agrega PHP 8.1 a la matriz de pruebas.
- Se configura [SonarCloud](https://sonarcloud.io/project/overview?id=phpcfdi_cfdi-cleaner).
- Se remueve Scrutinizer CI. Gracias por todo.
- Se actualizan los *badges* del proyecto.

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
