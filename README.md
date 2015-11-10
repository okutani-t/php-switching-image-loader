# PHP SWITCHING IMAGE LOADER

PHPで利用できる、用意した画像を日付ごとに切り替えるやつ

---

## 説明

ディレクトリに格納された画像を読み込み、一日おきに切り替える。
切り替えたい時間を指定できる。
基準となる日付けを用意して、現在の日付けを比較することで、その値を使って画像を切り替えている。

たとえば

2015/1/1(基準日)～2015/1/11(現在)　→　差は10

10 % 3(画像の枚数) = 1
→ 次の日は2、その次は割り切れるので0...
→ これらを添え字として使って、画像を切り替えている。

## 使い方

```php
// SwitchingImageLoader読み込み
require_once(__DIR__."/SwitchingImageLoader.class.php");

// 画像ファイルまでのパス
$dirPath = __DIR__."/img/";

$imgName = SwitchingImageLoader::_()->setDirPath($dirPath)      #パス指定
                                    ->setExtension("png")       #使いたい画像の拡張子
                                    ->excludeStr("DSC")         #取り除きたいファイルに含まれた文字
                                    ->setBaseDate("2015/10/10") #切り替え用基準日
                                    ->switchTime("8:00")        #切り替え用時間
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
```

* setDirPath()

画像が置いてあるディレクトリまでのパスを指定
* setExtension()

画像の拡張子を指定
* excludeStr("DSC")

指定した文字が画像に含まれていたら、それらを除外する→複数指定OK
* setBaseDate("2015/10/10")

基準となる日付けを設置。日付けならなんでもOK。空だと1990/1/1がセットされる
* switchTime("8:00")

画像を切り替えたい時間をセット
* getImgName()

画像の名前を取得

author: okutani
