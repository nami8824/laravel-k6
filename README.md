# このリポジトリについて

k6 を多重送信を行い、競合状態が起きるか検証するためのプロジェクトです

https://github.com/ucan-lab/docker-laravel-handson

上記のリポジトリの構成を基にしていて、 nginx, php-fpm のコンテナで laravel のアプリケーションが実行されます

## 環境構築手順

このリポジトリを clone する

```sh
git clone https://github.com/nami8824/laravel-k6.git
```

プロジェクト直下に移動する

```sh
cd laravel-k6
```

docker を起動する

```sh
docker compose up -d --build
```

`php-fpm`が稼働しているコンテナに入る

```sh
docker compose exec -it app bash
```

`composer install`を行う

```sh
composer install
```

`.env`ファイルをコピーで作成する

```sh
cp .env.example .env
```

キーを作成する

```sh
php artisan key:generate
```

テーブルを作成する

```sh
php artisan migrate
```

最後に、サーバー側でリクエストを同時に処理したいので、php-fpm でプロセスが複数起動されているか確認する

プロセスの数を確認する

```sh
ps aux
```

出力結果で`USER`が`www-data`のプロセスが 2 つあれば OK

コンテナから出る

```sh
exit
```

## 多重送信する

k6 で多重送信する

```sh
docker run --rm -i grafana/k6:latest-with-browser run --vus 2 --duration 10s - < k6/script.js
```

実行が終わったら k6 のイメージが残っているので削除する

```sh
docker image rm grafana/k6:latest-with-browser
```

## テーブルの中身を見てみる

DB のコンテナに入る

```sh
docker compose exec -it db bash
```

データベースにアクセスする

```sh
mysql -u phper -psecret laravel
```

多重送信によって作成されたレコードを確認する

```sh
select * from accounts;
```

`accounts`テーブルに`name`のカラム値が重複したレコードが存在してれば、競合状態が発生していることを検証できる
