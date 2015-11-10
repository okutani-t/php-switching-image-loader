<?php
// SwitchingImageLoader読み込み
require_once(__DIR__."/SwitchingImageLoader.class.php");
// デバッグ用
function d()
{
    echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px;line-height:18px">';
    foreach (func_get_args() as $v) {
        var_dump($v);
    }
    echo '</pre>';
}

// 画像ファイルまでのパス
$dirPath = __DIR__."/img/";

$imgName = SwitchingImageLoader::_()->setDirPath($dirPath)      #パス指定
                                    ->setExtension("png")       #使いたい画像の拡張子
                                    ->excludeStr("DSC")         #取り除きたいファイルに含まれた文字
                                    ->setBaseDate("2015/10/9") #切り替え用基準日
                                    ->switchTime("17:20")        #切り替え用時間
                                    ->getImgName();             #画像名の取得

// 相対パスでセット
$imgPath = "./img/".$imgName;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SwitchingImageLoader test</title>
</head>
<style>
body {
    width: 940px;
    margin: 0 auto;
}
</style>
<body>
    <h1>Switching Image Loaderのテスト</h1>
    <img src="<?= $imgPath ?>" width="200" height="200">
</body>
</html>
