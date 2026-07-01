# Medicare homework Appointment api

## Requirements
- sqlite (Can be changed to mysql from env)

## Install
- run `php artisan migrate` this will create the db and run the migrations
- run `composer run dev` to start the dev api

## Docs
- /docs/swagger.yml
- Route: [Swagger endpoint](http://localhost:8000/docs) here an interactive swagger UI

## Architecture
I have chosen a "lazy" layered and DDD architecture. The architecture layer rules are not fully enforced, which reduces the overhead on simpler feature flows, for example the get single resource endpoint.

The planned api flow is: Http -> Domain -> DataSource

I've tried to use Laravel's features which are simplified most of the code.

In the http layer I chose to have Actions instead of big controllers. In my opinion it is more readable and compact.

### Note on the domain layer
In the domain layer I tried to create separate(folders) domains. This prevents to have too much dependency on different features.
Tha main benefit is that every feature encapsulates itself with its own dependencies.

When there is a cross dependecy, its reasonable to create a bridge(or other) patterns which limits the interaction between different features.
This limits the dependency between them, also if the features are properly encapsulated then in case of a feature growing too big for this api then it could be moved into a different one, without too much hassle.

### The layers responsibilities:
- Http
    Parsing and validating the request with a main focus on the format of the request. (There are some cases where some business rules are here)
- Domain
    Main business logic. This Layer should be as indepenent as possible from the other layers.
- DataSource
    Is to give a data layer to be used in the Domain layer. Main reason is to have the domain layer to not depend on data source implementation.
    For this api its _mostly_ true.

## Notes on improvements
- Jobs
    There could be a business rule reason to have a cron job to regularly check the _Appointments_ statuses and chenge them. For example: When the appointment end_time is smaller than the current time. The system could change that status into Completed or _Missed_
    Also a DB cleanup and old Appointments into an archive table.
- Slots
    In my opinion it would simplify the api complexity if the slots wouldn't be calculated from Avaibility, but during the creation of said resource the app would create the appointment entities with a baseline status.
    It would reduce the error probability from developers.
- Caching
    Especially Slot listing endpoint could be cached, so the costly calculation is limited.
