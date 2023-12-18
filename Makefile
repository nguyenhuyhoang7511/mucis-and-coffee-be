up:
	docker compose --env-file .env.docker up -d

down:
	docker compose --env-file .env.docker down

exec:
	docker compose exec api bash

pull-dev:
	git pull origin dev

pull-master:
	git pull origin master

deploy-dev: pull-dev
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker build
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker stop
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker start

deploy: pull-master
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker build
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker stop
	sudo docker compose -f docker-compose.prod.yml --env-file .env.docker start
