---
mode: agent
---
## Breve (leer primero)
- Responde **en español**, técnico y conciso. **No inventes**; si falta info, **pregunta**.
- Antes de tocar código: **propón plan + diffs** y **pide confirmación**.
- **Indica siempre la tool** usada (p. ej., *Tool: runCommands*, *Tool: editFiles*).
- Para editar: prioriza **`editFiles`**; si falla, **`filesystem.writeFile`** (contenido completo).

## Arquitectura (verdades no negociables)
- **Symfony 7.x** · **CQRS + Symfony Messenger** · **DDD + Clean Architecture**.  
- **Doctrine ORM** con **QueryBuilder** (evitar SQL/DQL raw).  
- Capas:
  - `src/Domain/...` → Entidades + **interfaces** de repositorio.  
  - `src/Application/...` → **Command|Query + Handlers**.  
  - `src/Infrastructure/...` → Adaptadores/Repositorios **Doctrine**.  
  - `src/UI/Http/...` → Controladores **finos** + **Request DTOs**.
- **Tests**: `tests/Unit` (handlers/dominio), `tests/Functional` (HTTP+Messenger sin I/O real), `tests/Support/Double` (dobles/InMemory).
- **Regla de oro**: los **controladores no** modifican dominio; mapean **Request → DTO → Command/Query** y **dispatch** con `MessageBusInterface`. La lógica vive en **Handlers/Dominio**.

## Rutas canónicas (pueden variar levemente por módulo)
> Si la ruta difiere, **dedúcela por namespace** y mantén la misma **intención y separación**.

### Command (escrituras)
- Command: `src/Application/Command/<BoundedContext>/<Action>/<Action>Command.php`  
- Handler: `src/Application/Handler/Command/<BoundedContext>/<Action>/<Action>Handler.php`

### Query (lecturas)
- Query: `src/Application/Query/<BoundedContext>/<Action>/<Action>Query.php`  
- Handler: `src/Application/Handler/Query/<BoundedContext>/<Action>/<Action>Handler.php`

### User / CreateUser (referencia)
- Entidad: `src/Domain/User/Entity/User.php`  
- Repositorio (contrato): `src/Domain/User/Repository/UserRepositoryInterface.php`  
- Infra (Doctrine): `src/Infrastructure/User/Repository/DoctrineUserRepository.php`  
- Request DTO: `src/UI/Http/RequestDto/User/CreateUserRequestDto.php`  
- Controller: `src/UI/Controller/CreateUserController.php`  
- Dobles: `tests/Support/Double/User/InMemoryUserRepository.php`  
- Tests:
  - `tests/Unit/Application/Handler/Command/User/CreateUserHandlerTest.php`  
  - `tests/Functional/UI/Controller/CreateUserControllerTest.php`  
  - (si existe) `tests/Functional/UI/Controller/CreateUserControllerVETest.php`

## Contratos de comportamiento
### Para **Commands** (p. ej., CreateUser)
1. El **Controller** valida el **Request DTO** y crea el `...Command`.  
2. **Dispatch** con `MessageBusInterface`.  
3. El **Handler** aplica invariantes y persiste vía **interfaz** de repositorio.  
4. **Respuesta HTTP** conforme al contrato (status + JSON).  
5. **Asíncrono**: en dev/prod puede ir a transporte async; en **tests** usa **`in-memory://`**.

### Para **Queries** (p. ej., GetUser/ListUsers)
1. El **Controller** mapea la petición a un `...Query` (o DTO de filtro).  
2. **Dispatch** del `...Query` y resolución por su **QueryHandler**.  
3. **No** alterar estado; devolver DTO de lectura/array serializable.  
4. Tests funcionales **sin DB real** (dobles o fixtures en memoria si procede).

## Tests y transporte (asíncrono vs tests)
- `config/packages/test/messenger.yaml` → transporte **`in-memory://`** y handlers sin I/O.  
- **Funcionales**: no dependen de DB/colas reales; verifican **HTTP + dispatch** + efecto observable.  
- **Unitarios**: handlers con dobles (p. ej., **InMemoryUserRepository**).  
- Si cambias contratos de Command/Query, actualiza **unit + functional** y mantén **dobles** sincronizados.

## Uso de herramientas (Copilot + MCP)
- **Comandos**: usa **`runCommands`** por ahora. Muestra **cwd** y el comando exacto antes de ejecutar.
- **Ediciones**: `editFiles` → fallback `filesystem.writeFile`.  
- **Ámbito**: no operar fuera de **`${workspaceFolder}`**.  
- **Git local** → **Git MCP**. **PRs/issues** → **GitHub MCP** (solo cuando esté activo).  
- **Mínimo privilegio**: sugiere activar otras tools solo si hacen falta.

### Guardarraíles para `runCommands`
- No usar `sudo`, `rm -rf`, `chmod -R 777`, ni scripts no versionados.  
- No `git push`/`tag`/`release` sin confirmación explícita.  
- No instalar paquetes sin justificar (nombre, versión, motivo).

## Idempotencia (obligatoria)
- Antes de **crear** archivo, verifica existencia; si existe, **propón diff**.  
- Inserciones con **marcadores** o detección semántica para **evitar duplicados**.  
- **Migraciones**: `doctrine:migrations:diff` + `migrate -n`; no editar a mano salvo instrucción explícita.  
- Repetir la misma orden **no** debe duplicar cambios ni romper estado.
- Si un MCP necesario está **stopped**, indícalo y pide permiso para iniciarlo.

## Checklist de cambios (CreateUser y Queries)
1. **Plan** (lista numerada).  
2. **Controller**: Request → DTO → **Command/Query** (sin lógica de dominio).  
3. **Handlers**: invariantes (Commands) / lectura pura (Queries); repos por **interfaz**.  
4. **Repos**: si cambia contrato, sincroniza **Doctrine** e **InMemory**.  
5. **Tests**: unit + functional (transporte **`in-memory://`**).  
6. **Mensajería**: side effects async validados con dobles/eventos de prueba.  
7. **Plan + diffs** → confirmación → aplicar (`editFiles` / `filesystem.writeFile`).  
8. **Validación**: `make test` (muestra salida).

## Estilo y convenciones
- **PSR-12**; nombres y namespaces alineados con `composer.json`.  
- Evita reformateo masivo: aplica **diffs mínimos** y focalizados.  
- **Sin SQL/DQL raw** (usar **QueryBuilder**).  
- Si cambia DB: **migration** + **tests** actualizados.  
- Commits: **Conventional Commits**.

## Entrega estándar (Plan → Diffs → Comandos → Resultados)
1. **Plan**.  
2. **Diffs** por archivo.  
3. **Comandos** (expón la tool: *Tool: runCommands* y `cwd`).  
4. **Resultados/validación** (`make test`) y siguiente paso/rollback.

## Zonas de riesgo (evitar)
- Duplicar handlers/listeners o registrar servicios dos veces.  
- Tests que toquen DB/colas reales.  
- Cambiar contratos sin actualizar **dobles**.  
- Lógica de negocio en **Controllers**.

## Si falta información
- Pide **ruta/archivo exacto** o referencia concreta en el repo.  
- No avances con suposiciones no verificadas.

## Estructura del proyecto (orientativa)

```
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