# VehiMant

Gestión de vehículos y mantenimientos

## Descripción

VehiMant es una aplicación desarrollada en Symfony para la gestión de vehículos y sus mantenimientos. Utiliza Doctrine para la persistencia, Docker para la infraestructura y PostgreSQL como base de datos. Incluye testing unitario con PHPUnit y herramientas de desarrollo como pgAdmin y Mailpit.

## Stack tecnológico

- **Symfony** (PHP 8.3)
- **Doctrine ORM**
- **Docker & Docker Compose**
- **PostgreSQL 16**
- **pgAdmin4** (gestión visual de la base de datos)
- **Mailpit** (testing de emails)
- **PHPUnit** (tests unitarios)

## Arquitectura

El proyecto sigue los principios de **Clean Architecture** y **Domain-Driven Design (DDD)**.  
La estructura se organiza en capas claras:

- **Domain**: entidades y repositorios.
- **Application**: casos de uso (commands, queries, handlers).
- **Infrastructure**: implementaciones técnicas (Doctrine, etc.).
- **UI**: controladores (API/HTTP).
- **Tests**: unitarios, integración y funcionales.

## Estructura del proyecto

```
projectVehiMant/
├── src/
│   ├── Application/
│   │   ├── Command/
│   │   │   └── Vehicle/
│   │   │       ├── CreateVehicle/
│   │   │       ├── UpdateOdometer/
│   │   │       ├── RegisterMaintenance/
│   │   │       └── DeleteVehicle/
│   │   ├── Query/
│   │   │   └── Vehicle/
│   │   │       ├── GetVehicle/
│   │   │       │   └── GetVehicleQuery.php
│   │   │       ├── ListVehiclesByOwner/
│   │   │       │   └── ListVehiclesByOwnerQuery.php
│   │   │       └── DTO/
│   │   └── Handler/
│   │       ├── Command/
│   │       │   └── Vehicle/
│   │       │       ├── CreateVehicle/
│   │       │       ├── UpdateOdometer/
│   │       │       ├── RegisterMaintenance/
│   │       │       └── DeleteVehicle/
│   │       └── Query/
│   │           └── Vehicle/
│   │               ├── GetVehicle/
│   │               │   └── GetVehicleHandler.php
│   │               └── ListVehiclesByOwner/
│   │                   └── ListVehiclesByOwnerHandler.php
│   ├── Domain/
│   │   └── Vehicle/
│   │       ├── Entity/
│   │       │   ├── Vehicle.php
│   │       │   └── MaintenanceType.php
│   │       └── Repository/
│   │           └── VehicleRepositoryInterface.php
│   ├── Infrastructure/
│   │   └── Vehicle/
│   │       └── Repository/
│   │           └── DoctrineVehicleRepository.php
│   └── UI/
│       └── Controller/
│           └── VehicleController.php
├── tests/
│   ├── Unit/
│   │   └── Application/
│   │       └── Query/
│   │           └── Vehicle/
│   │               ├── GetVehicle/
│   │               │   └── GetVehicleHandlerTest.php
│   │               └── ListVehiclesByOwner/
│   │                   └── ListVehiclesByOwnerHandlerTest.php
│   ├── Integration/
│   ├── Functional/
│   └── Support/
│       └── Double/
│           └── Vehicle/
├── templates/
├── translations/
├── var/
├── vendor/
├── .editorconfig
├── .env
├── .env.dev
├── .env.local
├── .env.test
├── .gitignore
├── compose.override.yaml
├── compose.yaml
├── composer.json
├── composer.lock
├── docker-compose.yml
├── importmap.php
├── Makefile
├── phpunit.dist.xml
└── symfony.lock
```

## Instalación y arranque rápido

1. **Clona el repositorio**
   ```bash
   git clone <url>
   cd projectVehiMant
   ```

2. **Configura variables de entorno**
   Crea o edita el archivo `.env.local` en la raíz del proyecto con la siguiente línea:
   ```env
   DATABASE_URL="postgresql://vehimant_user:vehimant_pass@127.0.0.1:5433/vehimant_db?serverVersion=16&charset=utf8"
   ```

3. **Arranca los servicios con Docker Compose**
   ```bash
   docker compose up -d
   ```

4. **Instala dependencias PHP (dentro del contenedor vehimant-app)**
   ```bash
   docker compose exec app composer install
   ```

5. **Migra la base de datos**
   ```bash
   docker compose exec app php bin/console doctrine:migrations:migrate -n
   ```

6. **Ejecuta los tests unitarios**
   ```bash
   docker compose exec app php bin/phpunit --testdox
   ```

## Nota de puertos

- **Host/Windows/WSL:**  
  Conéctate a la base de datos en `127.0.0.1:5433`.
- **Dentro de Docker (pgAdmin):**  
  Usa el host `vehimant-postgres` y el puerto `5432`.

## Servicios Docker

- **vehimant-postgres:** Base de datos principal PostgreSQL.
- **vehimant-pgadmin:** Interfaz web para gestión de la base de datos (http://localhost:5050).
- **vehimant-app:** Contenedor PHP para ejecutar comandos y tests.
- **vehimant-mailer:** Mailpit para pruebas de envío de emails (http://localhost:8025).

## Comandos frecuentes

- Levantar servicios:  
  `docker compose up -d`
- Parar servicios:  
  `docker compose down`
- Ver estado de servicios:  
  `docker compose ps`
- Migrar base de datos:  
  `docker compose exec app php bin/console doctrine:migrations:migrate -n`
- Validar esquema:  
  `docker compose exec app php bin/console doctrine:schema:validate`
- Ejecutar tests unitarios:  
  `docker compose exec app php bin/phpunit --testdox`

## Testing

- Unit tests con PHPUnit (`tests/Unit`).

## Buenas prácticas

- **No versionar `.env.local`**: contiene credenciales y configuración local.
- **Usar migraciones** para cambios en el esquema de la base de datos.
- **Revisar los logs** de los contenedores con `docker logs <nombre-contenedor>` ante cualquier problema.

## Contribución

1. Haz un fork del repositorio.
2. Crea una rama para tu feature/fix.
3. Haz tus cambios y tests.
4. Envía un pull request.

## Licencia

Este proyecto está bajo la licencia MIT.
