

# README #

## UIライブラリ ###
---
* Vuetify3

### 1. クローン〜コンテナ立ち上げ
```sh
// クローンする
git clone https://thihaeung@bitbucket.org/fastgo/bloom-web.git

// プロジェクトディレクトリに移動
cd bloom-web

// コンテナ立ち上げ
docker compose up -d
```

### 2. Laravelの設定
```
// コンテナに入る
docker container exec -it bloom-php-fpm bash

// envファイル生成
cp .env.example .env

// Laravelをインストール
composer install

アプリケーションキーを生成
php artisan key:generate

// マイグレーションの実行
php artisan migrate --seed

php artisan storage:link

// フロントエンドの依存関係をインストール
yarn install

// フロントエンドサーバー立ち上げ
yarn dev
```
下記URLからアクセス  
[http://localhost:80](http://localhost:80)

