# API Implementations
* All API implementations MUST be build using API Platform
* All APIs MUST be secured, unless anonymous access is explicitly requested by the user.
* APIs MUST NOT interact directly with domain objects - DTOs MUST be used instead.
  * DTOs MAY be segregated into Input and Output DTOs where necessary.
* APIs are considered part of the application layer. They MUST NOT interact with the database save through the Service layer.
* Security must be handled using Symfony voters, e.g. ```\App\Security\CurrentUserOwnsCart```
  * Voters MAY cover multiple conceptually-related checks, e.g. User Cart ownership and User Cart Line Ownership

## Nested API Resources
- Nested API resources MUST declare URI variable metadata that matches the actual shape of the resource being serialized.
- You MUST NOT define nested item routes in a way that requires API Platform to infer parent identifiers from fields that do not exist on the output DTO.
- If an output DTO only exposes scalar parent identifiers, nested item operations MUST use URI variable definitions that can be resolved from those scalar fields.
- When a nested collection returns item DTOs, the collection operation MUST define `itemUriTemplate` for the corresponding nested item route.
- When a write operation (`POST`, and where applicable `PATCH`) returns an item DTO for a nested resource, that operation MUST define `itemUriTemplate` for the corresponding nested item route.
- Nested API resources SHOULD expose explicit scalar identifier fields for all route parameters needed to build their canonical item IRI.
- If a nested resource cannot provide the identifiers required for its canonical item route, the route design MUST be revised before implementation.

## API Platform DTO Design
- Output DTOs MUST expose the identifiers required for API Platform to generate their canonical IRI.
- If an output DTO is used as an item resource, its item identifier MUST be explicit and unambiguous.
- Collection and item operations for the same resource MUST agree on the canonical item route used for IRI generation.
- API tests for collection endpoints SHOULD assert the returned data shape in a way that is robust to API Platform collection wrappers unless the response format is explicitly fixed as part of the contract.
