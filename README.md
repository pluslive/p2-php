# rep2 expack

なんだかんだで歴史の長い、PHPでつくられたサーバーサイド2ちゃんねるビューワーです。

作りがレガシーなのは作っているひとがいちばんよくわかっているので、勘弁してつかあさい。

[次世代版建設予定地](https://github.com/rsky/page2)


## セットアップ

### Git & Composerで

1. 本体をclone
  <pre>git clone git://github.com/rsky/p2-php.git
  cd p2-php</pre>

2. 依存ライブラリをダウンロード
  <pre>git submodule update --init
  curl -O http://getcomposer.org/composer.phar
  php -d detect_unicode=0 composer.phar install</pre>

3. Webサーバが書き込めるようにディレクトリのアクセス権をセット  
  (CGI/suEXECIやCLI/Built-in web serverでは不要)
  <pre>chmod 0777 data/* rep2/ic</pre>


### Zipでくれ

つ[Tags](https://github.com/rsky/p2-php/tags)


## 動作環境
以下のコマンドを実行して、全ての項目で `OK` が出たなら大丈夫です。

何かエラーが出たらがんばって環境を整えてください。

    php scripts/p2cmd.php check


## Built-in web serverで使ってみる (PHP 5.4+)

PHP 5.4の新機能、[ビルトインウェブサーバー](http://docs.php.net/manual/ja/features.commandline.webserver.php)で簡単に試せます。

ルートディレクトリで以下のようにすると、Webサーバーの設定をしなくても `http://localhost:8080/` でrep2を使えます。**(Windowsでも!)**

    php -S localhost:8080 -t rep2 router.php

moriyoshi++


## 画像を自動で保存したい

スレに貼られている画像を自動で保存する機能、**ImageCache2**があります。

see also [doc/ImageCache2/README.txt](https://github.com/rsky/p2-php/blob/master/doc/ImageCache2/README.txt), [doc/ImageCache2/INSTALL.txt](https://github.com/rsky/p2-php/blob/master/doc/ImageCache2/INSTALL.txt)

### 準備

1. SQLite以外のデータベースを使う場合はデータベースサーバーを立ち上げておく。  

2. conf/conf_admin_ex.inc.phpでImageCache2を有効にする。
  <pre>$_conf['expack.ic2.enabled'] = 3;</pre>

3. conf/conf_ic2.inc.phpで[DSN](http://pear.php.net/manual/ja/package.database.db.intro-dsn.php)を設定する。
  <pre>$_conf['expack.ic2.general.dsn'] = 'mysql://username:password@localhost:3306/database';</pre>

4. setupスクリプトを実行する。
  <pre>php scripts/ic2.php setup</pre>

### 注意

* PHP 5.4ではSQLite2がサポートされなくなったので、ImageCache2を使いたいときはMySQLかPostgreSQLが必要です。
* ホストに`localhost`を指定して接続できないときは、代わりに`127.0.0.1`にしてみてください。


## 設定を変えたい

細かい挙動の変更は `メニュー > 設定管理 > ユーザー設定編集` から行えます。

Webブラウザから変更できない項目は [conf/conf_admin.inc.php](https://github.com/rsky/p2-php/blob/master/conf/conf_admin.inc.php) (基本), [conf/conf_admin_ex.inc.php](https://github.com/rsky/p2-php/blob/master/conf/conf_admin_ex.inc.php) (拡張パック), [conf/conf_ic2.inc.php](https://github.com/rsky/p2-php/blob/master/conf/conf_ic2.inc.php) (ImageCache2) を直接編集します。

どういうことができるか書き起こすのが面倒なので設定ファイルのコメントを見てください。


## 更新

### 本体

    git pull

### PEARライブラリ

    git submodule foreach 'git pull origin master'
    git submodule update

### composer.jsonが更新されたとき

    php -d detect_unicode=0 composer.phar update


## 古いバージョンからの移行

[144c0e91c](https://github.com/rsky/p2-php/commit/144c0e91c0822dc6ba5c237ec9759b3c98bd6a0d)でルートディレクトリにあったWeb経由でアクセスされるファイルをrep2ディレクトリに移動する等、ファイル構成を変更しました。

既存のデータをそのまま使い続けるには、conf/conf_user_admin.inc.php および conf/conf_ic2.inc.php を下記のように旧版相当の設定にします。

### conf/conf_user_admin.inc.php

※ dataディレクトリの位置はindex.phpがある階層よりひとつ上。

    $_conf['data_dir'] = P2_BASE_DIR . '/data';
    $_conf['dat_dir']  = P2_BASE_DIR . '/data';
    $_conf['idx_dir']  = P2_BASE_DIR . '/data';
    $_conf['pref_dir'] = P2_BASE_DIR . '/data';

### conf/conf_ic2.inc.php

    $_conf['expack.ic2.general.cachedir'] = P2_WWW_DIR . '/cache';    $_conf['expack.ic2.general.cacheuri'] = './cache';
    // 以下はSQLite2の場合のみ    $_conf['expack.ic2.general.dsn'] = 'sqlite:///' . P2_WWW_DIR . '/cache/imgcache.sqlite';

※ SQLite2な人は rep2/cache/imgcache.sqlite を data/db/imgcache.sqlite に移動して
DSNは `"sqlite:///{$_conf['db_dir']}/imgcache.sqlite"` がおすすめ。

## Authors & Contributors

* **aki** *(original)* http://akid.s17.xrea.com/
* **rsk** *(expack)* https://github.com/rsky/p2-php/
* **unpush** https://github.com/unpush/p2-php/
* **thermon** https://github.com/thermon/p2-php/
* **part32の892** *(+live)* http://plus-live.main.jp/
* **2ch p2/rep2スレの>>1-1000**


## License

see [LICENSE.txt](https://github.com/rsky/p2-php/blob/master/LICENSE.txt)
