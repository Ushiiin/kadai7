<?php

require_once "./dbc.php";

// ファイル関連の取得
$file = $_FILES['img'];
$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
// var_dump($file);
$upload_dir = 'images/';
$save_filename = date('YmdHis') . $filename;
$err_msgs = array();
$save_path = $upload_dir . $save_filename;

// キャプションの取得
$caption = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_SPECIAL_CHARS);

// キャプションのバリデーション
// 未入力
if (empty($caption)) {
    array_push($err_msgs, 'キャプションを入力してください');
};
// 140文字以下か
if (strlen($caption) > 140) {
    array_push($err_msgs, 'キャプションは140文字以上');
}

// ファイルのバリデーション
// ファイルサイズが1MB未満か. 超えている場合にはError2で返ってくる。
if ($filesize > 1048576 || $file_err == 2) {
    array_push($err_msgs, 'ファイルサイズは1MB未満');
}

// 拡張子は画像形式か
$allow_ext = array('jpg', 'jpeg', 'png');
$file_ext = pathinfo($filename, PATHINFO_EXTENSION);

if (!in_array(strtolower($file_ext), $allow_ext)) {
    array_push($err_msgs, '拡張子がまちがっている');
}

if (count($err_msgs) === 0) {
    // ファイルがあるかどうか
    if (is_uploaded_file($tmp_path)) {
        if (move_uploaded_file($tmp_path, $save_path)) {
            echo $filename . 'を' . $upload_dir .'にアップした';
            // DBに保存(ファイル名、ファイルパス、キャプション)
            $result = fileSave($filename, $save_path, $caption);
            if ($result) {
                echo 'アップロード成功！';
            } else {
                echo 'アップロード失敗！';
            }
        } else {
            echo 'ファイルのアップ失敗';
        }

    } else {
        echo 'ファイルが選択されていません';
    }
} else {
    foreach($err_msgs as $msg) {
        echo $msg;
        echo '<br>';
    }
}

?>
<a href="./index.php">戻る</a>