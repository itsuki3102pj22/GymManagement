

# 🏋️ GymManagement (PGMS: Personal Gym Management System)

科学的根拠に基づいた指導をサポートする、パーソナルジム向けの専用管理アプリケーションです。
トレーナーによる詳細な進捗管理と、LINE Messaging APIを活用した顧客への利便性提供を両立しています。

---

## 🚀 プロジェクト概要

本システムは、厚生労働省の「日本人の食事摂取基準（2025年版）」および
国立健康・栄養研究所の指標に基づき設計されています。

単なる記録ツールではなく、

* BMI目標値（18.5〜24.9）
* 身体活動レベル（PAL）

に応じたメンテナンスカロリー（EER）を自動算出し、
データに基づいた科学的なボディメイクを支援します。

---

## ✨ 主な機能

### 1. 🔐 権限別管理システム (Role Management)

* **責任者 (Supervisor)**
  ジム全体の売上、全トレーナー、全顧客データの閲覧・操作が可能

* **トレーナー (Trainer)**
  自身が担当する顧客のみを管理
  （計測入力 / トレーニング記録 / 予約管理）

* **顧客 (Client)**
  LINEを通じた食事報告、予約確認
  専用URLによる進捗閲覧

---

### 2. 📊 進捗管理・自動計算ロジック

* **身体評価**
  身長・体重からBMIを自動計算し、目標範囲内か評価

* **エネルギー管理**
  PALに基づいた推定エネルギー必要量（EER）を算出

* **未来予測グラフ**
  減量ペースから目標体重到達日を算出し、Chart.jsで可視化

* **トレーニングボリューム可視化**
  「重量 × 回数 × セット数」をグラフ化

---

### 3. 🍽 食事・トレーニング記録

* **LINE連携食事ログ**
  テキストから料理名を抽出し、食品マスタと照合
  → カロリー・PFCバランスを自動保存

* **セッション記録**
  種目 / 重量 / 回数 / 強度（強・中・弱）

* **コンディションノート**
  既往歴・体調（痛み / 睡眠など）を記録

---

### 4. 💳 予約・決済システム

* **ハイブリッド予約**
  管理画面入力 + LINE仮予約フロー

* **決済管理**
  Laravel Cashier（Stripe）によるサブスク管理

---

## 🛠 技術スタック

| 項目             | 内容                             |
| -------------- | ------------------------------ |
| Framework      | Laravel 11.x                   |
| Language       | PHP 8.2+, Blade                |
| Infrastructure | Docker / Laravel Sail          |
| Database       | MySQL                          |
| Frontend       | Tailwind CSS, Vite, Chart.js   |
| API            | LINE Messaging API, Stripe API |

---

## 📋 データベース設計

主要テーブル構成：

* `users`：管理者・トレーナー（Role管理）
* `clients`：顧客情報、UUID、病歴メモ
* `body_stats`：体重・BMI・体脂肪率など
* `workout_logs`：トレーニング記録
* `food_logs`：食事ログ（LINE連携）
* `reservations`：予約管理
* `payments`：決済履歴

---

## 🔧 セットアップ（Laravel Sail）

### ① リポジトリ取得

```bash
git clone https://github.com/itsuki3102pj22/GymManagement.git
cd GymManagement
```

### ② 環境構築

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
```

### ③ 初期設定

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

---
