<?php
/**
 * 画像読み込みの切り替えを行うクラス
 * 指定した時間に画像を切り替える(一日おき)
 *
 * @access public
 * @author okutani
 * @category ImageLoader
 * @package Class
 */
class SwitchingImageLoader
{
    /**
     * @var string $dirPath   画像があるディレクトリまでのパス
     * @var string $extention 読み込みたい画像の拡張子
     * @var array  $images    読み込んだ画像名の配列
     * @var string $baseDate  経過日数の基準となる日付け
     * @var int    $swtTimeNo 切り替え時刻のスイッチ(切り替え時刻を超えていなければ-1がセットされる)
     */
    private $dirPath   = "";
    private $extention = "";
    private $images    = array();
    private $baseDate  = "1990/1/1";
    private $swtTimeNo = 0;

    /**
     * 自身のインスタンスを生成
     * @access public
     * @return object new self
     */
    public static function _() {
        return new self;
    }

    /**
     * 読み込む画像までのパスをセット
     *
     * @access public
     * @param string $dirPath
     * @return object $this
     */
    public function setDirPath($dirPath="")
    {
        // 空チェック
        if ($dirPath === "") trigger_error("empty dirPath!", E_USER_NOTICE);

        // ディレクトリが存在するか確認
        if (is_dir($dirPath)) {
            $this->dirPath = $dirPath;
        } else {
            trigger_error("directory not found...", E_USER_NOTICE);
        }

        return $this;
    }

    /**
     * 拡張子を指定
     * 「.」があるか判断し、なければ自動で付属
     * ここで指定した拡張子を含む、画像のパスを格納している
     *
     * @access public
     * @param string $extention 空なら全ての画像ファイルを取得
     */
    public function setExtension($extention="")
    {
        // 引数が空なら全ての画像ファイルを取得
        if ($extention === "") {
            $this->extention = '\.(jpg|jpeg|png|gif)';
        // 画像ファイルか確認
        } elseif (!preg_match("/(jpg|jpeg|png|gif)$/i", $extention)) {
            trigger_error("arg is not extention...", E_USER_NOTICE);
        }

        // 全ての画像ならそのまま
        if ($this->extention === '\.(jpg|jpeg|png|gif)') {
        // 「.」付きか確認
        } elseif (mb_substr(trim($extention), 0, 1, "utf-8") !== ".") {
            $this->extention = ".".$extention;
        } else {
            $this->extention = $extention;
        }

        // ディレクトリが存在するか確認し、読み込み
        if (is_dir($this->dirPath) && $dh = opendir($this->dirPath)) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match( "/$this->extention$/i", $file)) {
                    $this->images[] = $file;
                }
            }
        } else {
            trigger_error("directory not found...", E_USER_NOTICE);
        }

        // 配列を自然順で昇順
        natsort($this->images);

        return $this;
    }

    /**
     * 特定の文字列を含んだ画像を除外する
     *
     * @access public
     * @param string args 除外したい画像が含んだ文字列
     * @return object $this
     */
    public function excludeStr()
    {
        // 複数の引数を取れるように
        foreach (func_get_args() as $excludeStr) {
            // 指定文字が含まれていたら空をセット
            foreach ($this->images as &$value) {
                if (preg_match("/$excludeStr/", $value)) {
                    $value = "";
                }
            }
        }

        // 空要素を取り除く無名関数の定義
        $rmEmptyStrFromAry = function(&$ary)
        {
            //配列の中の空要素を削除
            $ary = array_filter($ary, "strlen");
            //添字を振り直す
            $ary = array_values($ary);
        };

        // 空要素を取り除く
        $rmEmptyStrFromAry($this->images);

        return $this;
    }

    /**
     * 経過日数の基準となる$baseDateのセッター
     * デフォルト値は1990/1/1
     *
     * @access public
     * @param string args 除外したい画像が含んだ文字列
     * @return object $this
     */
    public function setBaseDate($baseDate="1990/1/1")
    {
        $this->baseDate = $baseDate;

        return $this;
    }

    /**
     * 切り替える時間を切り替える
     * 深夜00:00から指定時刻までの
     *
     * @access public
     * @return string $path 取得した画像パス
     */
    public function switchTime($switchTime="")
    {
        // スイッチする値が空ならエラー
        if ($switchTime === "") {
            trigger_error("switchDate is empty...", E_USER_NOTICE);
        }

        // 現在時刻のセット
        $nowTime = date("H:i", time());

        // 深夜00:00～switchTimeの間ならswtTimeNoに-1をセット
        if (strtotime($nowTime) >= strtotime("00:00") &&
            strtotime($nowTime) <= strtotime($switchTime)) {
            $this->swtTimeNo = -1;
        }

        return $this;
    }

    /**
     * 現在日時を基準にして画像パスを取得
     *
     * @access public
     * @return string $path 取得した画像パス
     */
    public function getImgName()
    {
        // 選択した画像の枚数を取得
        $imgCount = count($this->images);
        // 基準日と現在日時を比較して経過日数を取得
        $periodNo = $this->dayDiff($this->baseDate, date("Y/m/d", time()));

        // 深夜00:00～switchTimeの間なら経過日数をひとつずらす
        $periodNo += $this->swtTimeNo;

        // 配列の数で割ったあまりを添字として使う
        $indexNo = $periodNo % $imgCount;

        return $this->images[$indexNo];
    }

    /**
     * 基準日からの経過日数を取得する
     * 2038年問題？をクリアしていない
     *
     * @access private
     * @param  string $baseDate   基準になる日付(例:2015/1/1)
     * @param  string $targetDate 対象の日付(例:date("Y/m/d", time()))
     * @return string 経過日数
     */
    private function dayDiff($baseDate, $targetDate)
    {
        // 日付をUNIXタイムスタンプに変換
        $baseDate   = strtotime($baseDate);
        $targetDate = strtotime($targetDate);

        // 何秒離れているかを計算
        $secondDiff = abs($targetDate - $baseDate);

        // 戻り値
        return $secondDiff / (60 * 60 * 24);
    }

}
