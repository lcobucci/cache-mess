# Cache mess

This is a simple benchmark of PSR-16 and PSR-6 implementations.

## Running & cleaning up

Make sure you have Docker and `docker-compose` installed, then run:

```bash
$ docker-compose build \
    && docker-compose run --rm runner \
    && docker-compose down
```
