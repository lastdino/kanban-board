# カンバンボード

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4E56A6?style=flat-square&logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)

Laravelベースのカンバンボードパッケージです。Livewire 3とFlux UIを使用した、直感的なタスク管理システムを提供します。

## 要件

- PHP 8.0以上
- Laravel 12.0以上
- Livewire 3.0以上
- Livewire Flux 2.0以上
- Spatie Laravel Media Library 11.13以上
- AlpineJS 3.0以上
- @alpinejs/sort　3.0以上

## インストール

### 1. Composerでインストール

```bash
composer require lastdino/kanban-board
```

### 2. 設定ファイルの公開

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-config"
```

### 3. マイグレーションの実行

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-migrations"
php artisan migrate
```

### 4. ビューの公開（オプション）

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-views"
```

### 5. 言語ファイルの公開（オプション）

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-lang"
```

### 6. Spatie Media Library の設定

このパッケージはファイル添付機能にSpatie Media Libraryを使用しています。Spatie Media Libraryのインストールと設定については、[公式ドキュメント](https://spatie.be/docs/laravel-medialibrary/v11/installation-setup)を参照してください。
基本的な設定ステップ：

```bash
# Spatie Media Libraryのマイグレーションを公開
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"

# マイグレーションを実行
php artisan migrate

# 設定ファイルを公開（オプション）
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```


### 7. AlpineJS Sort Plugin のインストール
このパッケージはドラッグ＆ドロップ機能に '@alpinejs/sort' プラグインを使用しています。AlpineJS Sort Pluginのインストールについては、[公式ドキュメント](https://alpinejs.dev/plugins/sort)を参照してください。


## 機能

- **プロジェクト管理**: 複数のプロジェクトを同時に管理
- **カラム管理**: カスタマイズ可能なワークフローステップ
- **タスク管理**: ドラッグ＆ドロップでのタスク移動
- **バッジシステム**: タスクの分類とラベリング
- **コメント機能**: タスクへのコメント追加
- **サブタスク機能**: タスクの階層管理
- **チェックリスト**: タスク内のチェックリスト機能
- **ファイル添付**: Spatie Media Libraryを使用したファイル管理

## 設定

`config/kanban-board.php`で各種設定をカスタマイズできます：

```php
return [
    // ユーザーモデルの設定
    'users_model' => "\App\Models\User",

    // ルート設定
    'routes' => [
        'prefix' => 'kanban',
        'middleware' => ['web'],
        'guards' => ['web'],
    ],

    // 日付表示設定
    'datetime' => [
        'formats' => [
            'default' => 'Y-m-d H:i:s',
            'date' => 'Y-m-d',
            'time' => 'H:i:s',
            'year_month' => 'Y-m',
        ],
    ],

    // ユーザー表示設定
    'user' => [
        'display_name_column' => 'Full_name',
        'fallback_columns' => ['full_name', 'display_name', 'name'],
    ],
];
```

## 使用方法

### タスクリマインダーの送信

期限が近づいているタスクのリマインダーを送信するコマンドを提供しています。
```bash
php artisan kanban:send-reminders
```

## ライセンス

MITライセンスの下で公開されています。詳細は [LICENSE](LICENSE) ファイルをご覧ください。

## 作者

**Lastdino**
- GitHub: [github.com/lastdino](https://github.com/lastdino)
- Email: 87484368+lastdino@users.noreply.github.com
