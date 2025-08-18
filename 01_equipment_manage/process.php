<?php

// フォームから品番が送信されたか確認
if (!isset($_POST['itemNumber'])) {
    // 品番がない場合は、トップページへ戻す
    header('Location: main.html');
    exit;
}

$itemNumber = htmlspecialchars($_POST['itemNumber']);
$csvFile = 'database.csv';

// CSVファイルを読み込む
$rows = [];
if (($handle = fopen($csvFile, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $rows[] = $data;
    }
    fclose($handle);
} else {
    // ファイルが開けない場合のエラー処理
    die('CSVファイルを開けませんでした。ファイル名とパーミッションを確認してください。');
}

$found = false;

// 1行目（ヘッダー）を除いて、品番を検索
for ($i = 1; $i < count($rows); $i++) {
    if ($rows[$i][0] === $itemNumber) {
        // 品番が見つかったら「在庫有無」を「●」に更新
        $rows[$i][1] = '●';
        $found = true;
        break;
    }
}

// 品番が見つからなかった場合、新しい行を追加
if (!$found) {
    $newRow = [$itemNumber, '', '●'];
    $rows[] = $newRow;
}

// 更新後のデータをCSVファイルに書き込む
if (($handle = fopen($csvFile, 'w')) !== FALSE) {
    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
} else {
    // ファイルが開けない場合のエラー処理
    die('CSVファイルに書き込めませんでした。パーミッションを確認してください。');
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>処理結果</title>
    <style>
        body { font-family: sans-serif; margin: 2em; }
        .container { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; text-align: center; }
        .message { margin-top: 20px; padding: 10px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
    </style>
</head>
<body>

    <div class="container">
        <h2>処理完了</h2>
        <?php if ($found): ?>
            <p class="message success">品番 **<?php echo htmlspecialchars($itemNumber); ?>** の「在庫有無」を更新しました。</p>
        <?php else: ?>
            <p class="message success">新しい品番 **<?php echo htmlspecialchars($itemNumber); ?>** を登録しました。</p>
        <?php endif; ?>
        <p><a href="main.html">続けて登録する</a></p>
    </div>

</body>
</html>