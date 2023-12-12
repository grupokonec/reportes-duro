# Reportes AVO

## Indice

1. Requerimientos
2. Estructura
2. Instalacion
3. Como funciona

## Requerimientos

1. PHP >= 8.0
2. Composer 

## Estructura
```
└── 📁reportes
    └── .env
    └── 📁app
        └── 📁Controllers
            └── AllContactController.php
            └── CarteraController.php
            └── ExportCSVController.php
            └── ListaNegraController.php
            └── RegistroContactoController.php
        └── 📁Models
            └── Connection.php
    └── composer.json
    └── composer.lock
    └── index.php
    └── README.md
    └── 📁vendor
```

# Instalacion

Primero deben realizar un git clone del proyecto en la carpeta donde ejecutaran el proyecto
- Para Windows Xampp: C:/xampp/htdocs/
- Para linux Lamp: /var/www/html

```bash 
git clone https://github.com/grupokonec/reportes-duro.git
```

Luego deben ingresar en la carpeta del proyecto. 
```bash
cd reportes-duro
```

Ejecutar el comando `composer update` para instalar las librerias. 
``` bash 
composer update
```

Una vez instalado deben copiar el `.env.example` para la configuracion.
```bash 
cp .env.example .env
```

Luego abrir el `.env` en un editor de texto y complementar las variables. 

```.env
#Database config
HOST=<host_database>
DB=<name_dabatase>
USER=<user_database>
PASS=<password_database>
```

Ya hasta este punto deberia estar funcional el proyecto. 

### ¿Como funciona?
En la url de tu navegador debes colocar la siguente ruta: `http://localhost/reportes-duro?fecha=2023-01-10`, donde la variable `fecha` contiene la fecha a solicitar del reporte. 

Debes esperar a que se genere el reporte y se descargue automatico.

### Como realizar consultas: 

```php

#Inicializas la conexion 
use App\Models\Connection;

# Crear las variable para la conexion
$conexion = new Connection;

#preparas la consulta a realizar. 
$query = "SELECT * FROM bbdd.mi_table";

#ejecutas la consulta 
$resultados = $conexion->queryExe($query);
```