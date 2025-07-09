# カンバンボード

Laravelベースのカンバンボードパッケージです。

## インストール

Composerを使用してパッケージをインストールします：

```bash
composer require lastdino/kanban-board
```

## 設定

設定ファイルを公開します：

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-config"
```

## マイグレーション

データベースマイグレーションを実行します：

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-migrations"
php artisan migrate
```

## テストデータの生成

テスト用のシーダーを公開します：

```bash
php artisan vendor:publish --provider="Lastdino\KanbanBoard\KanbanBoardServiceProvider" --tag="kanban-seeders"
```

シーダーを実行してテストデータを生成します：

```bash
php artisan db:seed --class=KanbanBoardSeeder
```

## 機能

- プロジェクトの作成と管理
- カラム（ステータス）の管理
- タスクの作成と管理
- ドラッグ＆ドロップでのタスク移動
- バッジ（ラベル）によるタスクの分類
- サブタスク機能
- チェックリスト機能

## 使用方法

Livewireコンポーネントを使用してカンバンボードを表示します：

```blade
<livewire:kanban-board.board :board-id="1" />
```

## ライセンス

MITライセンスの下で公開されています。
