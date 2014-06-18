<?php
/**
 * ClickHeat : Fichier de langue : anglais
 * 
 * @author Yuta Yamashita - B-R-U - www.b-r-u.net
 * @since 29/03/2007
**/

define('LANG_TITLE', 'ClickHeat');
define('LANG_INDEX', '結果');
define('LANG_TOOLS', 'ツール');
define('LANG_AUTHORIZATION', 'このページはパスワード認証がかかっております。');
define('LANG_DELETE_LOGS', '右記の日数より古いログの削除');
define('LANG_DELETE_LOG_DIR', 'ログ及びログディレクトリの削除（clickhieatを削除したいときに使えます）');
define('LANG_UPDATE_OLD_LOGS', '古いログの更新（0.9より以前の場合）（セキュリティ向上のためのログフォーマット変更に伴う更新）');
define('LANG_DELETED_FILES', 'ファイルは削除されました');
define('LANG_RENAMED_FILES', 'ファイルの名前を変更しました');
define('LANG_LOG_DELETED', 'ログディレクトリが削除されました。');
define('LANG_LOG_NOT_DELETED', 'ログディレクトリを削除できませんでした。');
define('LANG_DAYS', '日数');
define('LANG_TO', 'から');
define('LANG_SURE', 'Sure');
define('LANG_UPDATE', '更新');
define('LANG_SAVE', '保存');
define('LANG_PAGE', 'ページ');
define('LANG_BROWSER', 'ブラウザ');
define('LANG_ALL', '全て');
define('LANG_UNKNOWN', 'その他/不明');
define('LANG_DATE', '期間');
define('LANG_OK', 'OK');
define('LANG_FOR_PAGE', '右記のページに関して');
define('LANG_EXAMPLE_URL', 'ウェブページ');
define('LANG_LAYOUT_WIDTH', 'レイアウトの幅: 左・中央・右');
define('LANG_DISPLAY_WIDTH', '結果を表示するサイズ');
define('LANG_SCREENSIZE', '画面のサイズ');
define('LANG_HEATMAP', 'ヒートマップの表示');
define('LANG_CHECK_LATEST', '最新バージョンの確認');
define('LANG_LATEST_VERSION', '最新バージョン');
define('LANG_YOUR_VERSION', 'あなたが利用しているバージョン');
define('LANG_NO_CLICK_BELOW', 'No clicks recorded beneath this line');
define('LANG_ERROR_PASSWORD', '警告！あなたはパスワードを指定していないか、パスワードが初期の状態になっていて誰でもアクセスができるようになっています。');
define('LANG_ERROR_PAGE', '不明なページ');
define('LANG_ERROR_DATA', '指定された期間のログはありません');
define('LANG_ERROR_FILE', 'ログファイルを開けませんでした');
define('LANG_ERROR_MEMORY', 'ini_get()によるメモリ限度を取得できませんでした。config.phpを確認してください。');
define('LANG_ERROR_PNG', 'PNGファイルが作成されていません');
define('LANG_ERROR_LOADING', '画像の生成中、お待ちください…');
define('LANG_ERROR_DIRECTORY', 'ログディレクトリがありません => <a href="check.php">check.php</a>をまず実行してください。');
define('LANG_ERROR_FIXED', '全ての幅が固定されています。そのような指定は無効です。上記のレイアウトの幅の数値の1つを変更してください。');
define('LANG_ERROR_TODAY', 'demoでは一部の人がサーバを荒らしてしまうので、本日のデータを取得することはできません。（今日のキャッシュは利用することができません）');
define('LANG_CHECKS', 'ClickHeat動作確認');
define('LANG_CHECKS_TO_BE', 'ClickHeatが正常に動作するには「あなたが利用しているシステム」以外の全ての行に「OK」と表示されていなければいけません。');
define('LANG_CHECK_SYSTEM', 'あなたが利用しているシステム');
define('LANG_CHECK_LOGPATH', 'ログディレクトリのパス');
define('LANG_CHECK_LOGPATH_DIR', 'ディレクトリを作成できませんでした。手動でディレクトリを作成してください。');
define('LANG_CHECK_LOGPATH_MKDIR', 'サブディレクトリを作成できませんでした。パーミッションを確認してください。（Apacheユーザにて書き込み権限がなくてはなりません）');
define('LANG_CHECK_LOGPATH_TOUCH', 'ファイルの最終アクセス時刻を更新することができませんでした。（通常は発生しません）（Windowsの場合はこのエラーが出る恐れがあります）');
define('LANG_CHECK_MEMORY', 'メモリの限度');
define('LANG_CHECK_MEMORY_BAD', 'ini_get()が有効ではありません。config.phpにて整数が指定されていません。php.iniに従ってconfig.phpのCLICKHEAT_MEMORYに値を指定してください。（MB単位で指定してください。php.iniのmemory_limitの値が\'8M\'である場合、整数の値である 8 を指定してください。)');
define('LANG_CHECK_MEMORY_INT', 'config.php内のCLICKHEAT_MEMORYの値は整数でなければいけません。（クオートない整数）');
define('LANG_CHECK_GD', 'GDライブラリ');
define('LANG_CHECK_GD_IMG', 'imagecreatetruecolor()が利用できません。画像を生成することができません。GDライブラリがインストールされているか確認してください。');
define('LANG_CHECK_GD_ALPHA', 'imagecolorallocatealpha()が利用できません。透過画像を生成することができません。（無視することもできますが、透過処理の利用は非常に推奨されます）');
define('LANG_CHECK_GD_PNG', 'imagepng()が利用できません。PNGファイルを生成することができません。');
define('LANG_CHECK_OK', 'OK');
$__jsHelp = array(
	'layout' => 'サイトのレイアウト: 0 = 自動の幅 それ以外はピクセル単位で指定<br />例: 左のメニューが 100px で固定で残りのスペースをコンテンツが利用している場合: 100 0 0<br />750px のコンテンツが中央にある場合（メニューなども750pxに含める場合）: 0 750 0<br />左のメニューが 100px で固定、コンテンツが左寄せで 650px の場合: 100 650 0 又は、全て左にあるので 750 0 0<br />左右にメニューが 100px で固定で、残りのスペースにコンテンツがある場合: 100 0 100<br />100% 全てがコンテンツ: 0 0 0<br /><br />もし、コンテンツが固定でない場合、クリック表示を正しい場所にするには、「画面のサイズ」を「結果を表示するサイズ」と同じにしてください。',
	'page' => 'Javascriptのコードをページに埋め込んだときに指定したタグ： initClickheat(\'ページ\');',
	'date' => 'レポートを表示する期間を YYYY-MM-DD のフォーマットで指定。もし、少量のデータしか無い場合、期間を広げることでデータを収集できます。',
	'heatmap' => 'レポート表示方法: クリックポジションのみ (デフォルト、非常に早い、左クリックが赤、右クリックが緑）又は、ヒートマップ（遅い、左クリックのみ）',
	'web' => '左記のページに対応するURLを指定してください。指定したページが下の結果に表示されます。デフォルトはおそらくルートパスである "../" です。絶対パス（http://www.my-site.com/page.html 又は単純に /page.html）又は相対パス（../page.html）が利用できます。'
);
?>
