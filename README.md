# VehiMant

- **Gestión de vehículos y mantenimientos.**

VehiMant es una aplicación para gestionar vehículos, propietarios y mantenimientos programados. Ofrece una API HTTP para crear y consultar recursos, aplica validaciones de entrada y procesa tareas asíncronas usando Symfony Messenger (patrón CQRS) para separar comandos y consultas.

- **Stack tecnológico**

  - PHP (>= 8.2)
  - Symfony 7.3.* (framework web)
  - Doctrine ORM
  - PostgreSQL 16
  - Symfony Messenger (CQRS, transports: doctrine/in-memory en tests)
  - Docker & Docker Compose (contenedores para app, Postgres, pgAdmin, Mailpit)
  - Composer (gestión de dependencias)
  - PHPUnit (tests unitarios y funcionales)
  - Makefile (scripts y targets: worker, worker-once, worker-setup, test)
  - Twig (plantillas)
  - Hotwire / Stimulus (assets/controllers)
  - Lexik JWT Authentication (autenticación JWT)
  - Mailpit (entorno de pruebas de correo)

## Nota de puertos

  - **Host/Windows/WSL:**  
    Conéctate a la base de datos en `127.0.0.1:5433`.
  - **Dentro de Docker (pgAdmin):**  
    Usa el host `vehimant-postgres` y el puerto `5432`.

## Servicios Docker

Los servicios definidos en el fichero de Docker Compose proporcionan el entorno de desarrollo completo. A continuación se explica el propósito de cada servicio y notas prácticas de uso:

- **vehimant-postgres** — PostgreSQL (base de datos). Escucha en el puerto `5432` dentro de la red de Docker; en el host suele mapearse a `127.0.0.1:5433` según la sección "Nota de puertos". Los datos deben persistir mediante volúmenes (configurado en compose).

- **vehimant-pgadmin** — pgAdmin (interfaz web para administrar PostgreSQL). Accede en `http://localhost:5050` desde el host. Cuando te conectes a la base de datos desde pgAdmin, usa el host `vehimant-postgres` y el puerto `5432` (dentro de Docker).

- **vehimant-app** — Contenedor de la aplicación PHP/Symfony. Aquí se ejecutan comandos como `composer install`, `php bin/console` y `phpunit`. 

- **vehimant-mailer** — Mailpit (captura de correo para desarrollo). Interfaz web en `http://localhost:8025` para ver los emails enviados por la aplicación en entornos de desarrollo.

- **Notas prácticas:**

  - Levantar todos los servicios:
    ```bash
    docker compose up -d
    ```
  - Parar y eliminar contenedores (sin borrar volúmenes):
    ```bash
    docker compose down
    ```
  - Ejecutar migraciones desde el contenedor app:
    ```bash
    docker compose exec app php bin/console doctrine:migrations:migrate -n
    ```
  - Inicializar tablas/transports de Messenger (según Makefile):
    ```bash
    docker compose exec app make worker-setup
    ```

- **Comandos útiles:**

- Ejecutar una consola dentro del contenedor:
  ```bash
  docker compose exec app bash
  ```
- Ejecutar comandos de Symfony o composer desde el host:
  ```bash
  docker compose exec app php bin/console <comando>
  docker compose exec app composer install
  ```

- Desde dentro del contenedor `app` recuerda usar el host `vehimant-postgres` para conectar con la base de datos y el puerto `5432`.

- Comprueba en `docker-compose.yml` (o `compose.yaml`) cómo están definidos los volúmenes si necesitas cambiar rutas de persistencia o los mapeos de puertos.

## Comandos frecuentes

  - **Levantar servicios:**  
    `docker compose up -d`
  - **Parar servicios:**  
    `docker compose down`
  - **Ver estado de servicios:**  
    `docker compose ps`
  - **Migrar base de datos:**  
    `docker compose exec app php bin/console doctrine:migrations:migrate -n`
  - **Validar esquema:**  
    `docker compose exec app php bin/console doctrine:schema:validate`
  - **Ejecutar tests:**  
    `docker compose exec app make test`

## Testing

- **Unit tests** con PHPUnit (`tests/Unit`).

- **Functional tests (resumen)**

  - En `tests/Functional/UI/Controller` hay tests que cubren el flujo HTTP y la integración con Messenger:

  - `CreateUserControllerTest.php` — Verifica que el endpoint `POST /api/new-user` encola el comando correctamente y devuelve 202 (queued).
  - `CreateUserControllerVETest.php` — Verifica las validaciones del request y que se devuelven errores 422 cuando los datos son inválidos.

  - Estos tests ejercitan el controlador, la validación y el comportamiento de encolado (en entorno de test el transporte es `in-memory://`).

- **Cómo ejecutar los tests**

  Usa el target del Makefile que ejecuta PHPUnit:

  ```bash
  make test
  ```

  Salida de ejemplo (ejecutado localmente):

  ```
  OK (9 tests, 38 assertions)
  ```

  Nota: si los tests no detectan cambios, limpia la cache de test:

  ```bash
  php bin/console cache:clear --env=test
  ```

![tests](https://img.shields.io/badge/tests-passing-brightgreen)

## Buenas prácticas

  - **No versionar `.env.local`**: contiene credenciales y configuración local.
  - **Añadir variables de entorno relacionadas con Messenger** en `.env.local` (`MESSENGER_TRANSPORT_DSN`, `MESSENGER_FAILURE_TRANSPORT_DSN`).
  - **Usar migraciones** para cambios en el esquema de la base de datos.
  - **Revisar los logs** de los contenedores con `docker logs <nombre-contenedor>` ante cualquier problema.
  - **Documentar los endpoints clave** (ejemplos curl) y los comandos `make` relacionados con Messenger en la sección correspondiente.

## Contribución

  1. Haz un fork del repositorio.
  2. Crea una rama para tu feature/fix.
  3. Haz tus cambios y tests.
  4. Envía un pull request.

## Endpoints clave

A continuación se listan los endpoints principales implementados en el proyecto:

- **POST /api/new-user**
  - Crea un nuevo usuario. Request JSON esperado:
    ```json
    {
        "email": "user@example.com",
        "plainPassword": "secret123"
    }
    ```
  - Respuestas:
    - 201: creado sin encolado (handler síncrono devuelve id).
    - 202: aceptado y encolado (respuesta con {"status":"queued","messageId":...}).
    - 422: errores de validación (campo `errors`).
    - 409: conflicto (por ejemplo email ya existente).

- **GET /api/me**
  - Devuelve los datos del usuario autenticado (id, email, roles).
  - Requiere autenticación (token JWT si está configurado). Responde 401 si no autorizado.

- **GET /api/vehicles/{id}**
  - Devuelve información de un vehículo por su id (ULID).

- **GET /api/owners/{ownerId}/vehicles**
  - Lista vehículos asociados a un owner (UUID).


## Estructura del proyecto

```text
projectVehiMant/
├── src/
│   ├── Application/
│   │   ├── Command/
│   │   │   ├── Vehicle/
│   │   │   │   ├── CreateVehicle/
│   │   │   │   ├── UpdateOdometer/
│   │   │   │   ├── RegisterMaintenance/
│   │   │   │   └── DeleteVehicle/
│   │   │   └── User/
│   │   │       ├── CreateUser/
│   │   │       ├── UpdateUser/
│   │   │       ├── RegisterUser/
│   │   │       ├── DeleteUser/
│   │   │       └── ChangePassword/
│   │   ├── Query/
│   │   │   ├── Vehicle/
│   │   │   │   ├── GetVehicle/
│   │   │   │   │   └── GetVehicleQuery.php
│   │   │   │   ├── ListVehiclesByOwner/
│   │   │   │   │   └── ListVehiclesByOwnerQuery.php
│   │   │   │   └── DTO/
│   │   │   └── User/
│   │   │       ├── GetUser/
│   │   │       ├── ListUsers/
│   │   │       ├── FindUserByEmail/
│   │   │       └── DTO/
│   │   └── Handler/
│   │       ├── Command/
│   │       │   ├── Vehicle/
│   │       │   │   ├── CreateVehicle/
│   │       │   │   ├── UpdateOdometer/
│   │       │   │   ├── RegisterMaintenance/
│   │       │   │   └── DeleteVehicle/
│   │       │   └── User/
│   │       │       ├── CreateUser/
│   │       │       ├── UpdateUser/
│   │       │       ├── RegisterUser/
│   │       │       ├── DeleteUser/
│   │       │       └── ChangePassword/
│   │       └── Query/
│   │           ├── Vehicle/
│   │           │   ├── GetVehicle/
│   │           │   │   └── GetVehicleHandler.php
│   │           │   └── ListVehiclesByOwner/
│   │           │       └── ListVehiclesByOwnerHandler.php
│   │           └── User/
│   │               ├── GetUser/
│   │               ├── ListUsers/
│   │               └── FindUserByEmail/
│   ├── Domain/
│   │   ├── Vehicle/
│   │   │   ├── Entity/
│   │   │   │   ├── Vehicle.php
│   │   │   │   └── MaintenanceType.php
│   │   │   └── Repository/
│   │   │       └── VehicleRepositoryInterface.php
│   │   └── User/
│   │       ├── Entity/
│   │       │   └── User.php
│   │       └── Repository/
│   │           └── UserRepositoryInterface.php
│   ├── Infrastructure/
│   │   ├── Vehicle/
│   │   │   └── Repository/
│   │   │       └── DoctrineVehicleRepository.php
│   │   └── User/
│   │       └── Repository/
│   │           └── DoctrineUserRepository.php
│   └── UI/
│       ├── Controller/
│       │   ├── VehicleController.php
│       │   └── AuthController.php
│       └── Http/
│           └── RequestDto/
│               ├── User/
│               │   └── CreateUserRequestDto.php
│               │   └── UpdateUserRequestDto.php
│               │   └── DeleteUserRequestDto.php
│               └── Vehicle/
│                   └── CreateVehicleRequestDto.php
│                   └── UpdateVehicleRequestDto.php
│                   └── DeleteVehicleRequestDto.php
├── tests/
│   ├── bootstrap.php
│   ├── Unit/
│   │   └── Query/
│   │       ├── Vehicle/
│   │       │   ├── GetVehicle/
│   │       │   │   └── GetVehicleHandlerTest.php
│   │       │   └── ListVehiclesByOwner/
│   │       │       └── ListVehiclesByOwnerHandlerTest.php
│   │       └── User/
│   │           ├── GetUser/
│   │           │   └── GetUserHandlerTest.php
│   │           └── ListUsers/
│   │               └── ListUsersHandlerTest.php
│   ├── Integration/
│   ├── Functional/
│   │   └── UI/
│   │       └── Controller/
│   │           ├── CreateUserControllerTest.php
│   │           └── CreateUserControllerVETest.php
│   └── Support/
│       └── Double/
│           ├── Vehicle/
│           └── User/
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
├── public/
├── bin/
└── symfony.lock
```

## Instalación y arranque rápido

1. **Clona el repositorio**
```bash
  git clone <url>
  cd projectVehiMant
  ```

2. **Configura variables de entorno**
  Crea o edita el archivo `.env.local` en la raíz del proyecto con:
  ```env
  DATABASE_URL="postgresql://vehimant_user:vehimant_pass@127.0.0.1:5433/vehimant_db?serverVersion=16&charset=utf8"
  MESSENGER_TRANSPORT_DSN=doctrine://default
  MESSENGER_FAILURE_TRANSPORT_DSN=doctrine://default?queue_name=failed
  ```

  También puedes copiar el archivo de ejemplo incluido en el repo y editarlo:

  ```bash
  cp .env.example .env.local
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

6. **Inicializa Messenger (tablas y transports)**
  ```bash
  docker compose exec app make worker-setup
  ```

7. **Ejecuta los tests unitarios y funcionales**
  ```bash
  docker compose exec app make test
  ```

8. **Procesa la cola Messenger (asíncrono)**
  ```bash
  docker compose exec app make worker
  ```

## Licencia

Este proyecto está bajo la licencia MIT.