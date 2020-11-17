# Requirements
- docker
- docker-compose

# Begin
1. Clone proejct: `git clone teachaway-challenge`
2. Up docker containers: `docker-compose up -d --build`
3. Create database: `docker exec -i teachaway-mysql mysql -uroot -psecret <<< "CREATE SCHEMA teachaway_swapi;"`