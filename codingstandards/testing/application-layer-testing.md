## Application Layer Testing
* Application layer testing MUST be written using Pest
* All controllers MUST have pest browser tests.
* All API resources MUST have tests.
* Controller tests MUST be stored in the `tests/ApplicationLayer/Controller` directory.
* API tests MUST be stored in the `tests/ApplicationLayer/API` directory.
* Browser tests MUST identify DOM elements by id, not by content or class.
* Application layer tests MUST use the test database, dnd-puzzles_test, NOT the real one.
* To reset the test database, run these two scripts in order:
  * ```tests/bootstrap/generate-schema-snapshot.sh```
  * ```tests/bootstrap/reset-test-db.sh```
* Once reset is complete on the host, run tests inside the container as usual.
