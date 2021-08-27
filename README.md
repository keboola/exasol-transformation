# Exasol transformation
[![GitHub Actions](https://github.com/keboola/exasol-transformation/actions/workflows/push.yml/badge.svg)](https://github.com/keboola/exasol-transformation/actions/workflows/push.yml)

Application which runs [KBC](https://connection.keboola.com/) transformations in Exasol DB.

## Development

Clone this repository and init the workspace with following command:

```sh
git clone https://github.com/keboola/exasol-transformation
cd exasol-transformation
docker-compose build
docker-compose run --rm dev composer install --no-scripts
```

Create `.env` file with following contents
```env
EXASOL_HOST=
EXASOL_PORT=
EXASOL_USERNAME=
EXASOL_PASSWORD=
EXASOL_SCHEMA=
```

To use a local instance of Exasol via docker-compose, enter:
```env
EXASOL_HOST=exasol
EXASOL_PORT=8563
EXASOL_USERNAME=sys
EXASOL_PASSWORD=exasol
EXASOL_SCHEMA=testSchema
```

Run for wait on Exasol DB
```sh
docker-compose run --rm wait
```

Run the test suite using this command:

```sh
docker-compose run --rm dev composer tests
```

# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/) 
