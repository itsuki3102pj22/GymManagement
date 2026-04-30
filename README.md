# 🏋️ GymManagement (PGMS: Personal Gym Management System)

科学的根拠に基づいた指導をサポートする、パーソナルジム向け専用管理システム。
**Laravel × LINE Messaging API × Chart.js** を活用し、
トレーナー業務効率化と顧客の継続支援を両立します。

---

## 🚀 プロジェクト概要

本システムは、厚生労働省「日本人の食事摂取基準（2025年版）」および
国立健康・栄養研究所の指標を参考に設計された、
**科学的データに基づくパーソナルジム管理アプリケーション** です。

単なる顧客管理ではなく、

* BMI評価
* 身体活動レベル（PAL）
* 推定エネルギー必要量（EER）
* 未来予測グラフ（目標達成予定）

を通じて、
**データドリブンなボディメイク支援** を目指しています。

---

## ✨ 主な機能

### 🔐 権限別管理システム（Role Management）

### Supervisor（責任者）

* 全顧客情報管理
* 全トレーナー管理
* ジム全体進捗確認

### Trainer（トレーナー）

* 担当顧客管理
* 身体測定入力
* トレーニング記録
* 予約管理

### Client（顧客）

* LINEによる食事報告
* LINE予約申請
* 専用URLによる進捗確認

---

## 📊 科学的進捗管理

### 身体分析

* BMI自動計算
* 目標BMI範囲判定

### エネルギー分析

* PALベースEER算出
* 減量 / 維持 / 増量目標管理

### グラフ分析

* 体重推移グラフ（Chart.js）
* 未来予測グラフ
* 目標達成予定日算出
* トレーニングボリューム可視化

---

## 🍽 食事・トレーニング管理

### LINE食事ログ

* LINE Messaging API連携
* 食事内容保存
* カロリー管理（予定）

### トレーニング管理

* 種目
* 重量
* 回数
* セット数
* 強度記録

### コンディション管理

* 睡眠
* 痛み
* 既往歴
* 体調メモ

---

## 📅 予約管理機能

### 予約フロー

* 管理画面予約登録
* LINE仮予約
* 仮予約 → 確定管理

---

## 🛠 技術スタック

| Category       | Technology                  |
| -------------- | --------------------------- |
| Framework      | Laravel 11                  |
| Backend        | PHP 8.2+                    |
| Frontend       | Blade / Tailwind CSS / Vite |
| Database       | MySQL                       |
| Infrastructure | Docker / Laravel Sail       |
| API            | LINE Messaging API          |
| Visualization  | Chart.js                    |

---

## 📋 データベース設計

* users（管理者 / トレーナー）
* clients（顧客情報）
* body_stats（身体測定）
* workout_logs（トレーニング記録）
* food_logs（食事ログ）
* reservations（予約）

---

## 🚀 セットアップ

### 1. Clone Repository

```bash
git clone https://github.com/itsuki3102pj22/GymManagement.git
cd GymManagement
```

### 2. Environment Setup

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
```

### 3. Initial Configuration

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

### 4. Frontend Build

```bash
npm install
npm run dev
```

---

## 📈 今後の開発予定

* AI食事画像解析
* PFC自動計算
* LINE完全自動化
* モバイル最適化
* 多店舗対応

---

## 🎯 開発目的

**経験則だけに頼らず、科学的根拠に基づくジム運営支援** を目的として開発。

---

## 👨‍💻 Developer

**Itsuki Matsuzaki**
Laravel / HealthTech / Web Application Development
