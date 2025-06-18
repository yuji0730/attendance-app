# 模擬案件中級＿勤怠管理アプリ

## 環境構築

Dockerビルド 
 1. `git clone git@github.com:yuji0730/attendance-app.git`
 2. `cd attendance-app`
 3. `docker-compose up -d--build`
 
laravel環境構築 
1. `docker-compose exec php bash`
2. `composer install`
3. .env.exampleファイルから.envを作成
4. .envに以下の環境変数を追加
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. `php artisan key:generate`
6. `php artisan migrate` 
7. `php artisan db:seed`


## 使用技術(実行環境)
* PHP 7.4.9
* Laravel 8.0
* MySQL 8.0.26



 
## URL 
* 開発環境:http://localhost/ 
* phpMyAdmin:http://localhost:8080/
