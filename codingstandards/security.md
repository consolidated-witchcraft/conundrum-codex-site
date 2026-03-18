# Security
## Input Handling & Validation
- All external input MUST be treated as untrusted until proven otherwise.
- Validation MUST occur at system boundaries
- Validation MUST be explicit and deterministic
- DTOs or value objects MUST be used for all inbound data.
- Validation of DTOs and Value Object MUST use Symfony validators
- Invalid input MUST be rejected immediately.

## Output Encoding & XSS Prevention
- Output MUST be escaped by default
- Use framework-native escaping (e.g. Twig autoescape) SHOULD be used where appropriate.
- Output MUST be encoded based on context (HTML, JS, URL)
- Unescaped data MUST NOT be injected into scripts or markup.

## Authentication & Authorization
- Authentication MUST precede authorization
- Authorization MUST be enforced server-side
- The principle of least privilege MUST be enforced
- Roles MUST NOT be used to establish a user's capabilities, only the permissions beneath that role.

## 4. Database Security
- Queries MUST be parameterized
- ORM bindings MUST be used.
- String concatenation MUST NOT be used for SQL Queries.
- When not working with complete entities, DTOs SHOULD be used to avoid exposing irrelevant information.

## Secrets & Configuration
- Secrets MUST NOT exist in code
- Secrets MUST NOT be logged
- Secrets MUST BE injected via environment variable.

## Logging & Error Handling
- Logs MUST NOT leak sensitive data
- Errors exposed to users MUST be safe
- Security-relevant events MUST be logged
- Use structured logging where possible

## CSRF & Request Integrity
- All state-changing operations MUST be protected by CSRF checks.
- GET MUST NOT be used to change state.

## File Upload & Handling
- File input MUST be treated as hostile
- Both MIME type AND extension MUST be validated
- Filesize limits MUST be enforced.

## Dependency & Supply Chain Security
- Dependencies MUST be trusted and maintained

## API & External Communication
- All External systems MUST be treated as untrusted
- All external responses MUST be validated
- Outbound requests SHOULD be validated (where applicable)


## Rate Limiting & Abuse Protection
- Public interfaces MUST be protected against abuse

## Principle of Least Knowledge

- Systems MUST expose minimal information
- DTOs or transformers MUST be used for output.
- Internal structure SHOULD NOT be exposed by API structures


## Enforcement Protocol

When reviewing or generating code, agents MUST:

1. Identify ALL security violations explicitly
2. Classify severity:
   - Critical
   - High
   - Moderate
   - Low
3. Provide a compliant alternative
4. Prefer the most secure viable implementation

### Automatic Refusal Conditions

Agents MUST refuse or flag code that includes:

- Hardcoded secrets
- Raw SQL string concatenation
- Missing authorization checks
- Disabled escaping without justification
- Unsafe deserialization
- Execution of user-provided input

---

## Symfony-Specific Enforcement

### Mandatory Patterns
- Use Voters for authorization logic
- Use DTOs between controllers and entities
- Do NOT expose Doctrine entities directly in APIs
- Use Symfony Validator for all input

### Security Headers (Required)
- Content-Security-Policy
- X-Frame-Options
- X-Content-Type-Options
- Strict-Transport-Security

### Violations
- Direct entity serialization in responses
- Business logic inside controllers
- Missing CSP or weak policies
