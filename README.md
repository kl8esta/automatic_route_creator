# 観光ルート自動作成アプリ
## アプリの内容
### (1)観光ルート自動作成
出発地と観光地を入力すると回る順番(最適な観光ルート)が自動的に出力される
### (2)観光ルートの投稿
* 作成されたルートを公開し、実際に観光してみた感想や、道路情報などをSNS形式で投稿
* 他の人が作成したルートを観光時の参考にしたり、いいね評価をすることが可能
## テストアカウント
* User Name: tester
* E-Mail Address(login): tester@test
* Password(login): test-test
## 作成背景
### 問題点
* あまり行ったことない都道府県の場合、どの観光スポットを回れば良いか分からない
* 多くの地図アプリは観光地の回る順番を手動で並び替える必要があり、自分で観光ルートを計画するのが大変

### アプリの目的
* 行きたい観光スポットをピックアップし、最適な観光ルートを自動で提示するようにする
* 他の人が投稿したルートや感想を参考にして、行きたい場所のイメージを膨らませられるようにする

## 環境
* OS: Windows10
* 言語: PHP, SQL, JavaScript, HTML
* フレームワーク: Laravel 6
* 使用API: Google Maps API

## 機能一覧(太字表記が現在実装済み)
### 　ログインページ
#### (1)アカウントログイン・新規登録
### 　マイページ
#### (2)マイページ(作成・保存したルート、ルート作成ページのリンク先などを記載)
### 　「ルートガイド」ページ

(マイページの「自分で観光地を決める」のクリックで遷移)
#### (3)検索した観光スポットの候補のマップ表示
* 「公園」など検索すると候補がマーカーで表示される
*  マーカーをクリックすると小ウィンドウが開き、その場所の写真・住所、平均レビュー、レビュー数の情報が見られる
#### (4)行きたい場所の「観光地リスト」追加機能
* 小ウィンドウ内の「目的地に追加」をクリックすると、その場所が観光地リストに追加される
#### (5)行きたい観光地をピックアップし、最適ルートを出力
* 「観光地リスト」下の「ルートを作成」ページボタンを押すと、「観光地リスト」内の観光地を回るルートが作成される
    * 「観光地リストの」1番目が「出発地」、最後が「最終目的地」となり、「出発地」→「経由地」→・・・→「最終目的地」のような最短時間のルートが表示される
    * 現在車のルートのみ使用可能 (徒歩 + 電車 のようなルートは日本国内では使用できない仕様)
### 　ルート投稿ページ

(「ルートガイド」ページで観光地を2つ以上追加して「ルートを作成」ボタンをクリックすると遷移)
#### (6)ルートの投稿
* (最適ルートマップの投稿は現在未実装)タイトル、感想、道路情報を入力し投稿
* 非公開保存(自分のみ閲覧可能or下書き)するか、公開するかを選択可能
### 　ルート一覧ページ
#### (7)投稿されたルートの一覧表示ページ
* タイトル、投稿者ユーザ名、最終更新、経路マップ(未実装)などの情報を記載
* 他人が投稿して全てのルートを見られる
* 自分が作成したルート投稿(公開・下書き全て)も見られる
### 　ルート詳細ページ
#### (8)ルート投稿詳細ページ(一覧ページでタイトル名をクリックすると移動)
* 最適ルートマップ(表示は現在未実装)、感想、道路情報(補足)を閲覧可能
#### (9)マイルートの編集・削除
(マイルートの詳細ページで実行可能)
* 下書きルートの再編集・削除を行う機能

(10)公開ルートにいいねをする
* 各公開ルートに「いいねボタン + いいね数」を実装、一覧ページで確認可能

## 工夫した点
* 小ウィンドウでデフォルトの「地名」だけでなく、観光地の「写真」「住所」「平均評価」「レビュー数」の情報を付与したこと。

→ユーザが観光地をよりイメージできるように変更

## 苦労していること
* Google Maps API を連携させてマップ表示する箇所、最適経路を計算しマップ出力する箇所
(APIのドキュメントを読みつつ実装中)
