# Security
## Authority
Security rules in this document are **binding**.

Agents must:
- Enforce these rules by default
- Identify violations explicitly before proposing solutions
- Prefer compliant implementations over convenient ones
- Refuse unsafe patterns unless explicitly authorised

---

## 1. Input Handling & Validation

### Rules
- ALL external input is untrusted
- Validation MUST occur at system boundaries
- Validation MUST be explicit and deterministic

### Requirements
- Use DTOs or value objects for all inbound data
- Apply strict validation rules (e.g. Symfony Validator)
- Reject invalid input immediately (fail fast)

### Violations
- Using raw request data inside business logic
- Implicit or partial validation
- Trusting client-provided identifiers or permissions

---

## 2. Output Encoding & XSS Prevention

### Rules
- Output MUST be escaped by default
- Unsafe rendering MUST be explicit and justified

### Requirements

- Use framework-native escaping (e.g. Twig autoescape)
- Encode output based on context (HTML, JS, URL)

### Violations
- Rendering raw user input
- Disabling escaping globally
- Injecting unescaped data into scripts or markup

---

## 3. Authentication & Authorization

### Rules

- Authentication MUST precede authorization
- Authorization MUST be enforced server-side
- Least privilege is mandatory

### Requirements

- Use role + permission systems
- Enforce access via voters or equivalent patterns
- Validate ownership of resources

### Violations

- Relying on frontend checks
- Role-only authorization without permissions
- Exposing data before authorization checks

---

## 4. Database Security

### Rules

- Queries MUST be parameterized
- ORM bindings are mandatory

### Requirements

- Use Doctrine or prepared statements exclusively
- Apply database constraints where possible

### Violations

- String concatenated SQL
- Trusting user input in queries
- Over-fetching sensitive data

---

## 5. Secrets & Configuration

### Rules

- Secrets MUST NOT exist in code
- Secrets MUST NOT be logged

### Requirements

- Use environment variables or secret managers
- Rotate credentials periodically

### Violations

- Hardcoded API keys, tokens, passwords
- Logging sensitive values

---

## 6. Logging & Error Handling

### Rules

- Logs MUST NOT leak sensitive data
- Errors exposed to users MUST be safe

### Requirements

- Log security-relevant events
- Use structured logging where possible

### Violations

- Returning stack traces in production
- Logging credentials or tokens

---

## 7. CSRF & Request Integrity

### Rules

- All state-changing operations MUST be protected

### Requirements

- Enforce CSRF protection
- Use correct HTTP verbs

### Violations

- Mutations via GET
- Missing CSRF tokens on forms

---

## 8. File Upload & Handling

### Rules

- File input MUST be treated as hostile

### Requirements

- Validate MIME type AND extension
- Enforce file size limits
- Store files outside web root where possible

### Violations

- Trusting file names
- Executing uploaded content

---

## 9. Dependency & Supply Chain Security

### Rules

- Dependencies MUST be trusted and maintained

### Requirements

- Keep dependencies up to date
- Review all new packages

### Violations

- Adding unverified dependencies
- Retaining unused packages

---

## 10. Secure Code Defaults

### Rules

- Security MUST be the default posture

### Requirements

- Default to private visibility
- Prefer immutability
- Require explicit opt-in for unsafe behaviour

### Violations

- Public-by-default design
- Implicit unsafe behaviour

---

## 11. API & External Communication

### Rules

- External systems are untrusted

### Requirements

- Validate all external responses
- Use timeouts and retry strategies
- Authenticate outbound requests where applicable

### Violations

- Blind trust in third-party data
- Leaking internal structure via APIs

---

## 12. Rate Limiting & Abuse Protection

### Rules

- Public interfaces MUST be protected against abuse

### Requirements

- Rate limit authentication endpoints
- Monitor suspicious activity

### Violations

- Unlimited login attempts
- No abuse detection mechanisms

---

## 13. Principle of Least Knowledge

### Rules

- Systems MUST expose minimal information

### Requirements

- Return only necessary data
- Use DTOs or transformers for output

### Violations

- Returning full entities unnecessarily
- Exposing internal identifiers or structure

---

# ⚖️ Enforcement Protocol

When reviewing or generating code, agents MUST:

1. Identify ALL security violations explicitly
2. Classify severity:
   - Critical
   - High
   - Moderate
   - Low
3. Provide a compliant alternative
4. Prefer the most secure viable implementation

---

# Automatic Refusal Conditions

Agents MUST refuse or flag code that includes:

- Hardcoded secrets
- Raw SQL string concatenation
- Missing authorization checks
- Disabled escaping without justification
- Unsafe deserialization
- Execution of user-provided input

---

# Symfony-Specific Enforcement

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
