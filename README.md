# Project Backend
Backend API Project - Laravel

## Requirements:
* docker >= 17.12.0+
* docker-compose

## Quick Start
* Clone or download this repository
* Go inside of directory,  `cd backend`
* Run this command `docker-compose up -d`
* Run this command for installing dependencies first time `./composer build`


# Environments

## Local Development Environment
APP_ENV=local - Used for Local Development. 

## How to Build or Run Docker Images
* Build all images using `docker-compose build` (If You already have images, Skip this)
* Start all containers using `docker-compose up -d`
* See logs using `docker-compose logs -f`
* Single command for all `docker-compose build && docker-compose up -d && docker-compose logs -f`
* Clear all existing containers `docker system prune -a`
* Stop all containers `docker-compose down`
* Rebuild all containers `docker-compose up -d --no-deps --build app`

## use composer/artisan in workspace directly
* use `./composer {command}` to run any composer command without long docker commands. i.e `./composer install`
* use `./php-artisan {command}` to run any artisan command without long docker commands. i.e `./php-artisan key:generate`

## Default Env Variables : 


## Working Directory : 
* Root Folder itself

## Access to API : 
* **URL:** `http://localhost:8009`
* **Swagger Docs:** `http://localhost:8009/api/documentation`

## Access to Mysql DB: 
Data is mounted to seperate docker volume
* **HOST:** `db:3309`
* **Username:** projectdb (as a default)
* **Password:** apsRs1cLxAvEdd3dd (as a default)

## Access to PHPMyAdmin: 
* **URL:** `http://localhost:8209`
* **Username:** projectdb (as a default)
* **Password:** apsRs1cLxAvEdd3dd (as a default)

## Mysql Config:
my.cnf file will be automatically mounted from docker-compose folder
