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

## 使用技術

## APIエンドポイント一覧

## 開発環境URL

## 作成者

山崎 達也
