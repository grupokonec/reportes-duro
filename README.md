# Reportes




Como realizar consultas: 

```php

#Inicializas la conexion 
use App\Models\Connection;

# Crear las variable para la conexion
$conexion = new Connection;

#preparas la consulta a realizar. 
$query = "Select * from mi_table";

#ejecutas la consulta 
$resultados = $conexion->queryExe($query);
```


## Descripcion de funciones

### `queryExe($query, $params = [])`: 

Esta funcion dispone de 2 parametros para su funcionamiento
(* Requerido)

Query: en este se debe armar la consulta. *

```php
$query = "SELECT * FROM mi_table"; 
```

Params: Este es en caso de que se tenga una consulta con filtros

```php
$query = "SELECT * FROM mi_table where campo1 = :valor1"; 
$param = [
    ':valor1' => 'condicion1',
];

```

## consultas con in 

```php 
$consulta = new Connection;

$idList = ['086', '087'];

// Generar la consulta SQL de manera más sencilla
$query = "SELECT * FROM motivo_nopago WHERE id IN ('" . implode("', '", $idList) . "')";

// Ejecutar la consulta utilizando la clase Connection
$resultados = $consulta->queryExe($query);
```



```sql
SELECT
    ac.rut,
    ac.tipo,
    ac.respuesta,
    ac.fecha,
    ac.feccomp,
    ac.telefono,
    ac.glosa,
    c.IDEmpresaCobranza,
    c.MesAsignacion,
    c.Segmento,
    c.IDCampana,
    c.IDGrupoControl,
    c.Contrato
FROM
    all_contacts ac
INNER JOIN (
    SELECT
        c1.rutsd,
        c1.IDEmpresaCobranza,
        c1.MesAsignacion,
        c1.Segmento,
        c1.IDCampana,
        c1.IDGrupoControl,
        c1.Contrato
    FROM



        cartera_primer_dia c1
    WHERE
        c1.rutsd = (
            SELECT c2.rutsd
            FROM cartera_primer_dia c2
            WHERE c2.rutsd = c1.rutsd
            ORDER BY c2.FechaVencimiento
            LIMIT 1
        )
) AS c ON ac.rut = c.rutsd
WHERE
    fecha LIKE '2023-09-29%'
    AND tipo IN ('AU', 'EM', 'SM', 'VI')
    AND ac.rut IN ('76732358');
    ```



    SELECT
	ac.rut,
	ac.tipo,
	ac.respuesta,
	date(ac.fecha),
	ac.feccomp,
	ac.telefono,
	ac.glosa,
	c.IDEmpresaCobranza,
	c.MesAsignacion,
	c.Segmento,
	c.IDCampana,
	c.IDGrupoControl,
	c.Contrato
FROM
	all_contacts ac
INNER JOIN (
	SELECT
    c1.rutsd,
    c1.IDEmpresaCobranza,
    c1.MesAsignacion,
    c1.Segmento,
    c1.IDCampana,
    c1.IDGrupoControl,
    c1.Contrato
FROM cartera_primer_dia c1
WHERE c1.RUTSD IN (
        SELECT c2.RUTSD
        FROM cartera_primer_dia c2
        WHERE
            c2.FechaVencimiento IN (
                SELECT
                    max(c3.fechavencimiento) AS fecha
                FROM cartera_primer_dia c3
                GROUP BY
                    c3.fechavencimiento
            )
    )
) AS c ON ac.rut = c.rutsd
WHERE
	date(fecha) LIKE "' . $fecha . '"
AND tipo IN ("AU", "EM", "SM", "VI")
LIMIT 10


SELECT
	c1.rutsd,
	c1.IDEmpresaCobranza,
	c1.MesAsignacion,
	c1.Segmento,
	c1.IDCampana,
	c1.IDGrupoControl,
	c1.Contrato,
	a.rut,
	a.tipo,
	a.respuesta,
	date(a.fecha),
	a.feccomp,
	a.telefono,
	a.glosa
FROM
	cartera_primer_dia c1
INNER JOIN (
	SELECT
		ac.rut,
		ac.tipo,
		ac.respuesta,
		ac.fecha,
		ac.feccomp,
		ac.telefono,
		ac.glosa
	FROM
		all_contacts ac
) AS a ON c1.RUTSD = a.rut
where a.tipo IN ("AU", "EM", "SM", "VI")
and date(a.fecha) = "2023-09-30"
HAVING
	IDCampana > 0
