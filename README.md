# COACHTECH

## 概要

### 主な機能

## ER図

erDiagram

    users {
        bigint_unsigned id PK
        varchar_255 name
        varchar_255 email UK
        timestamp email_verified_at
        varchar_255 password
        varchar_100 remember_token
        timestamp created_at
        timestamp updated_at
    }

    books {
        bigint_unsigned id PK
        bigint_unsigned user_id FK
        varchar_255 title
        varchar_255 author
        varchar_13 isbn UK
        date published_date
        text description
        varchar_255 image_url
        timestamp created_at
        timestamp updated_at
    }

    genres {
        bigint_unsigned id PK
        varchar_255 name UK
        timestamp created_at
        timestamp updated_at
    }

    reviews {
        bigint_unsigned id PK
        bigint_unsigned book_id FK "UNIQUE(book_id, user_id)"
        bigint_unsigned user_id FK
        tinyint rating
        text comment
        timestamp created_at
        timestamp updated_at
    }

    favorites {
        bigint_unsigned id PK
        bigint_unsigned book_id FK "UNIQUE(book_id, user_id)"
        bigint_unsigned user_id FK
        timestamp created_at
        timestamp updated_at
    }

    review_likes {
        bigint_unsigned id PK
        bigint_unsigned review_id FK "UNIQUE(review_id, user_id)"
        bigint_unsigned user_id FK
        timestamp created_at
        timestamp updated_at
    }

    book_genre {
        bigint_unsigned id PK
        bigint_unsigned book_id FK "UNIQUE(book_id, genre_id)"
        bigint_unsigned genre_id FK
        timestamp created_at
        timestamp updated_at
    }

    reading_plans {
        bigint_unsigned id PK
        bigint_unsigned book_id FK "UNIQUE(book_id, user_id)"
        bigint_unsigned user_id FK
        date target_date
        string status
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }

    notifications {
        uuid id PK
        string type
        string notifiable_type
        bigint_unsigned notifiable_id
        text data
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ books : "has many"
    users ||--o{ reviews : "has many"
    books ||--o{ reviews : "has many"

    users ||--o{ favorites : "has many"
    books ||--o{ favorites : "has many"

    users ||--o{ review_likes : "has many"
    reviews ||--o{ review_likes : "has many"

    books ||--o{ book_genre : "has many"
    genres ||--o{ book_genre : "has many"

    users ||--o{ reading_plans : "has many"
    books ||--o{ reading_plans : "has many"

    users ||--o{ notifications : "has many"

## 環境構築手順

### 1. リポジトリをクローン

git clone <repository-url>
cd <project-directory>

### 2. .envを作成

    cp .env.example .env

### 3. Dockerコンテナを起動

    ./vendor/bin/sail up -d

### 4. アプリケーションキーを生成

    ./vendor/bin/sail artisan key:generate

### 5. マイグレーションを実行

    ./vendor/bin/sail artisan migrate

### 6. シーダーを実行

    ./vendor/bin/sail artisan db:seed

### 7. Laravelのキャッシュをクリア

    ./vendor/bin/sail artisan optimize:clear

### 8. Schedulerを起動

    読書計画の期限切れ更新やリマインダー通知は、LaravelのSchedulerによって定期的に実行されます。
    開発環境では、別ターミナルで以下を実行してください。

            ./vendor/bin/sail artisan schedule:work

        以下のように表示されればSchedulerが起動しています。
        INFO Running scheduled tasks every minute.

    Schedulerを起動していない場合、読書計画の期限切れ更新やリマインダー通知は自動実行されません。

終了する場合は Ctrl + C を押してください。

読書計画のバッチ処理は毎日00:00に実行されます。

### 手動でのバッチ実行

読書計画の期限状態更新やリマインダー通知は、以下のコマンドで手動実行することもできます。

```bash
./vendor/bin/sail artisan reading-plans:process-reminders
```

動作確認や、Schedulerが実行されているか確認する場合に使用してください。

## Google Books APIの設定

本アプリでは、ISBNから書籍情報を取得するためにGoogle Books APIを使用しています。ISBN検索機能を利用するには、アプリのセットアップ時に利用者自身でGoogle Books APIのAPIキーを取得し、環境変数に設定してください。

### 1. Google Cloud Consoleでプロジェクトを作成

[Google Cloud Console](https://console.cloud.google.com/) にアクセスし、Googleアカウントでログインします。

既存のプロジェクトを使用する場合は、そのプロジェクトを選択してください。

新しく作成する場合は、プロジェクトを作成してください。

### 2. Google Books APIを有効化

Google Cloud Consoleで対象のプロジェクトを選択した状態で、

**「APIとサービス」→「ライブラリ」**

を開きます。

「Google Books API」を検索し、対象のAPIを開いて**「有効にする」**をクリックしてください。

### 3. APIキーを作成

「APIとサービス」→「認証情報」を開きます。

**「認証情報を作成」→「APIキー」**

を選択すると、APIキーが作成されます。

作成されたAPIキーを確認してください。

> APIキーは第三者に公開しないでください。
> GitリポジトリやREADMEなどに直接記載しないでください。

### 4. `.env`にAPIキーを設定

プロジェクトの`.env`ファイルに以下を追加してください。

```env
GOOGLE_BOOKS_API_KEY=取得したAPIキー
```

### 5. `config/services.php`を確認

以下の設定でAPIキーを読み込みます。

```php
'google_books' => [
    'api_key' => env('GOOGLE_BOOKS_API_KEY'),
],
```

### 6. 設定キャッシュをクリア

Laravel Sailを使用している場合は、以下を実行してください。

```bash
./vendor/bin/sail artisan config:clear
```

### 7. ISBN検索を確認

書籍登録画面からISBNを入力して検索し、Google Books APIから書籍情報が取得できることを確認してください。

取得できる書籍情報は、APIの検索結果によって異なります。

### APIキーについて

APIキーは開発者個人のものではなく、**実際に本アプリを利用・運用する環境の管理者が自身のGoogle Cloudプロジェクトで取得したものを使用してください。**

このリポジトリにはAPIキーを含めていません。

また、`.env`ファイルはGitにコミットしないでください。

## 使用技術

- PHP
- Laravel
- Laravel Sanctum
- Laravel Fortify
- Laravel Sail
- Docker
- MySQL
- Nginx
- Vite
- Tailwind CSS
- Google Books API

## APIエンドポイント一覧

## 開発環境URL

- アプリケーション: http://localhost
- Vite開発サーバー: http://localhost:5173
- phpMyAdmin: http://localhost:8080

## 作成者

山崎　達也
