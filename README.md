# SAE Lectures | WebDev | PHP.CMS "Simple CMS Application"
This repository provides a very basic CMS example application with minimized dependencies 
running inside a dockerized PHP environment and providing

* a MYSQL database with initial fixture data
* the composer package manager
* Twig templating engine support

# Build Dev-Environment with Docker
`docker compose up --force-recreate`

# Test it in the browser of your computer
Call this URL in your web browser:
`http://localhost:8080`

# Check running containers
You can check whether the container is running by executing
`docker container list`

This will return a list of all containers with their corresponding container IDs

# Kill (stop) a running container
To kill a running container, execute
`docker kill <container_id>`

# Open Interactive Terminal to container
To enter a running container via bash, execute
`docker exec -it sae-php-cms bash`

# Sign in as Test User
In order to log in as test user, please call this URL:
`http://localhost:8080/?action=login_show`
and log in with these test credentials:
username `testuser1` and password `12345`

# Execute predefined PHPUNIT-tests
To run the defined phpunit tests, please execute this instruction in your terminal:
`./vendor/phpunit/phpunit/phpunit tests`