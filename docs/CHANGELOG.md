# CHANGELOG

## SemVer 2.0

Utilizamos [Versionado Semántico 2.0.0](SEMVER.md).

## Cambios en la rama principal sin liberación de nueva versión.

Los cambios no liberados se integran a la rama principal, pero no requieren de la liberación de una nueva versión.

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
  utilizando la definición simple `xmlns`. Además elimina los namespace superfluos y las 
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
