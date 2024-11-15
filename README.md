

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
php artisan install:reverb

// Put these email config to .env

MAIL_MAILER=smtp
MAIL_HOST=sv8713.xserver.jp
MAIL_PORT=465
MAIL_USERNAME=thihaaung@voyager-web.com
MAIL_PASSWORD=t5ShxELk
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=thihaaung@voyager-web.com
MAIL_FROM_NAME="${APP_NAME}"

IMAP_HOST=sv8713.xserver.jp
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=thihaaung@voyager-web.com
IMAP_PASSWORD=t5ShxELk
IMAP_PROTOCOL=imap

// Open Next Tab In Terminal

php artisan reverb:start --port=8081

php artisan queue:work

php artisan schedule:run

下記URLからアクセス  
[http://localhost:80](http://localhost:80)

