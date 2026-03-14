## Architectural standards

### Code Organisation
* You MUST follow Domain-Driven Design Principles
* Keep controllers thin, use service classes.
* All new services MUST injected.
* All new services MUST created in 'services' directory

### Service Layer
* Each service SHOULD be responsible for a single domain area.
* Both top-level and subservice names follow this pattern: '{service name}Service', e.g PuzzleService for Puzzles and OrderService for orders.
* Top-Level Services MUST be created in the service directory with the naming pattern src/Services/{service name}/Service/{service name}.php
        * Services that are a dependency of other services are subservices, and are created in the service directory with the following naming pattern: src/Services/{service name}/Service/{subservice name}.php
    * The name of Folder the top-level service is in must be capitalized, and obey PSR-4 naming conventions.
    * Each service MUST have an interface.
    * Each interface MUST be created in the service directory with the naming pattern src/Services/{service name}/Service/Interfaces/{service name}Interface.php for top-level services and ./src/Services/{service name}/Service/Interfaces/{subservice name}Interface.php for subservices
    * Services MUST have a concrete implementation.
    * Services MUST only contain business logic that cannot be implemented in the domain objects themselves.
    * Services MUST be the only part of the system that are allowed to directly interact with repositories.
    * Any repository a service requires MUST be injected into the service.
    * Any calls to a service's public methods MUST obey the law of Demeter.
    * Services MUST be injected into controllers via interface.

### Domain Layer
* Domain objects should be created in the should be created in the service directory with the naming pattern src/Services/{service name}/Domain/{domain object name}.php
* Domain objects MUST be mutable
* Value objects MUST NOT be mutable
* All domain objects MUST be unit tested.
* All properties of domain objects MUST be protected or private, and MUST be declared in the constructor via constructor uplift.
* It must NOT be possible to create a domain object in an invalid state. If we need to gather partial information in order to build a full object, we should use a DTO, validate it, then use its data to build a complete object.
* Domain behavior SHOULD be encapsulated in domain objects.
* Domain objects MUST not allow their data to be put into an invalid state.
* Domain objects MAY be extended by other domain objects.
* Domain objects MUST object SOLID principles.
* Domain objects MAY be doctrine objects.
* Domain objects MAY be read-only
* Domain objects MUST NOT be final.

### Infrastructure Layer
* The infrastructure layer MUST be the only layer that should be aware of the underlying database.
* The infrastructure layer MUST be responsible for handling database transactions and ensuring data consistency.
* Services MUST be the only part of the system that are allowed to directly interact with repositories.
* Repositories MUST be injected into services via interface.
* Repositories MUST be created in the service directory with the naming pattern src/Services/{service name}/Infrastructure/{repository name}.php

### Application Layer
* The application layer MUST be the only layer that should be aware of the user interface.
* The application layer MUST be responsible for handling user input and output.
* The application layer SHOULD be responsible for handling user errors.
* Controllers SHOULD be the only part of the system that are allowed to directly interact with services.
* Controllers MUST have services injected into them via interface.
* When building data to be turned into a new domain object, controllers MUST use DTOs.
* Controllers are considered part of the application layer.
* API endpoints are considered part of the application layer.
