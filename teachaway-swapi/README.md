## Teachaway challenge - Extending "The Star Wars API" (SWAPI)

### Description
**SWAPI** provides information about the Star Wars universe.<br>
This implementation extends SWAPI and enrich with inventory of starships and vehicles.<br>

### How it works?
When you make a request, this API will request to /vehicles or /starships from SWAPI and automatically go to every available page and count results, then save to database with payload.<br>
Every endpoint has its own validations that makes you easier to know how to use it. 

### Requirements
- Docker
- Docker Compose

### Important
The docker-compose.yml file use 8080 port for API and 3306 port for database.

### Inventory resources

#### GET /inventory<br>
* This resource will match all vehicles or starships tha partial **match** with **name** and model property and response with json like:
``` 
curl --location --request GET 'http://0.0.0.0:8080/api/inventory?unit_type=vehicle&tags=Death%20Star' --header 'Accept: application/json'
{
    "id": 1,
    "unit_type": "vehicle",
    "criteria": "partial_match",
    "tag": "star",
    "count": 3,
    "payload": [...],
    "created_at": "2020-11-18 07:20:39",
    "updated_at": "2020-11-18 07:20:39",
}
```

### PATCH /inventory/{id}
* Set the number Death Stars in the inventory of starships:<br>
**NOTE**: if you modify count, this would not match with same criteria.
```
curl --request PATCH 'http://0.0.0.0:8080/api/inventory/1' --header 'Accept: application/json' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'count=15'
{
    "id": 1,
    "unit_type": "vehicle",
    "criteria": "partial_match",
    "tag": "star",
    "count": 15,
    "payload": [...],
    "created_at": "2020-11-18 07:20:39",
    "updated_at": "2020-11-18 07:20:39",
}
```

## POST /inventory/{id}/increment
* Increment the total number of units for a specific starship or vehicle:
**NOTE**: if you increment count, this would not match with SWAPI same criteria.
```
curl --location --request POST 'http://0.0.0.0:8080/api/inventory/1/increment' --header 'Accept: application/json'
{
    "id": 1,
    "unit_type": "vehicle",
    "criteria": "partial_match",
    "tag": "star",
    "count": 16,
    "payload": [...],
    "created_at": "2020-11-18 07:20:39",
    "updated_at": "2020-11-18 07:20:39",
}
```

## POST /inventory/{id}/decrement
* Decrement the total number of units for a specific starship or vehicle:
**NOTE**: if you decrement count, this would not match with SWAPI same criteria.
```
curl --location --request POST 'http://0.0.0.0:8080/api/inventory/1/decrement' --header 'Accept: application/json'
{
    "id": 1,
    "unit_type": "vehicle",
    "criteria": "partial_match",
    "tag": "star",
    "count": 15,
    "payload": [...],
    "created_at": "2020-11-18 07:20:39",
    "updated_at": "2020-11-18 07:20:39",
}
```

### How to make it work
1. Clone project:
    ```shell script
    git clone https://github.com/emiliosa/teachaway-challenge.git
    ```
2. Docker up containers, go to docker/ path and run:<br>
    ```shell script
    docker-compose up -d --build
    ```
3. Run composer: 
    ```shell script
    docker exec -i teachaway-swapi composer install
    ```
4. Create schema: 
    ```shell script
    docker exec -i teachaway-mysql mysql -uroot -psecret <<< "CREATE SCHEMA teachaway_swapi;"
    ```
5. Run migrations: 
    ```shell script
    docker exec -i teachaway-swapi php artisan migrate:refresh
    ```
6. Run test (unit + feature):
    ```shell script
    docker exec -i teachaway-swapi php vendor/phpunit/phpunit/phpunit --testsuite Unit --debug
    docker exec -i teachaway-swapi php vendor/phpunit/phpunit/phpunit --testsuite Feature --debug
    ```
7. Make request:<br>
You can hit API using http://0.0.0.0:8080/api/inventory 

### TODO
- Add multiple tag's (e.g: 'passengers_count', 'films_count', GTE (greater than or equal) passangers attribute's value).
- Add JWT authorization.
