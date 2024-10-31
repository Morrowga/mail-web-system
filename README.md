

# README #

## UIライブラリ ###
---
* Vuetify3

### 1. クローン〜コンテナ立ち上げ
```sh
// クローンする
git clone git@bitbucket.org:fastgo/lemone-matching.git

// プロジェクトディレクトリに移動
cd lemone-matching

// docker-compose.yamlで使用する環境変数ファイルを生成
cp .env.sample .env

// 各環境に合わせて.envを編集する
vi .env

// コンテナ立ち上げ
docker compose up -d
```

### 2. Laravelの設定
```
// コンテナに入る
docker container exec -it lm-php-fpm bash

// envファイル生成
cp .env.example .env

// Laravelをインストール
composer install

アプリケーションキーを生成
php artisan key:generate

// マイグレーションの実行
php artisan migrate

// シーディングの実行
php artisan db:seed

// フロントエンドの依存関係をインストール
yarn install

// フロントエンドサーバー立ち上げ
yarn dev
```
下記URLからアクセス  
[http://localhost:80](http://localhost:80)

