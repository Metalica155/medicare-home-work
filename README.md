# Medicare Homework – Appointment API

## Requirements

* SQLite (can be changed to MySQL via the `.env` configuration)

## Installation

* Run `php artisan migrate` to create the database and execute the migrations.
* Run `composer run dev` to start the development server.

## Documentation

* Swagger specification: `/docs/swagger.yml`
* Interactive Swagger UI: http://localhost:8000/docs

## Architecture

I chose a "lightweight" layered architecture inspired by Domain-Driven Design (DDD). The layer boundaries are not enforced strictly, which reduces overhead for simpler feature flows, such as retrieving a single resource.

The planned request flow is:

**HTTP → Domain → DataSource**

I also tried to leverage Laravel's built-in features wherever possible to keep the implementation concise.

In the HTTP layer, I chose to use **Actions** instead of large controllers. In my opinion, this makes the codebase easier to read, navigate, and maintain.

### Notes on the Domain layer

Within the Domain layer, I separated the application into feature-specific domains. This helps prevent excessive coupling between different parts of the application.

The main benefit is that each feature encapsulates its own business logic and dependencies.

When cross-feature communication is required, it would be reasonable to introduce bridge (or similar) patterns to limit direct dependencies between features.

Keeping features properly encapsulated also makes it easier to extract a feature into a separate service in the future, should the application grow beyond the scope of this API.

## Layer Responsibilities

### HTTP

Responsible for parsing and validating incoming requests, with a primary focus on request format and input validation. Some lightweight business validation is also performed where appropriate.

### Domain

Contains the application's core business logic. This layer should remain as independent as possible from infrastructure and framework-specific concerns.

### DataSource

Provides the persistence layer used by the Domain. Its primary purpose is to prevent the Domain from depending directly on a specific persistence implementation.

For this assignment, this separation is mostly maintained.

## Possible Improvements

### Background Jobs

A scheduled job could periodically update appointment statuses based on business rules.

For example, when an appointment's `end_time` is earlier than the current time, its status could automatically transition to **Completed** or **Missed**.

A scheduled cleanup process could also archive old appointments into a dedicated archive table.

### Slots

In my opinion, the API could be simplified by persisting appointment slots when an availability is created, rather than generating them dynamically each time they are requested.

This would reduce runtime complexity and lower the likelihood of introducing bugs in slot generation logic.

### Caching

The slot listing endpoint is a good candidate for caching, as generating slots can become computationally expensive. Caching would reduce unnecessary recalculation and improve response times.
