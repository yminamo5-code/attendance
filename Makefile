init:
	docker compose up -d --build
	docker compose exec laravel.test composer install
	docker compose exec laravel.test cp .env.example .env
	docker compose exec laravel.test php artisan key:generate
	docker compose exec laravel.test php artisan migrate:fresh --seed
	docker compose exec laravel.test php artisan storage:link
	docker compose exec laravel.test chmod -R 777 storage bootstrap/cache

up:
	docker compose up -d

down:
	docker compose down

fresh:
	docker compose exec laravel.test php artisan migrate:fresh --seed