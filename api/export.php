<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 导出功能对所有人开放，不需要验证令牌

$format = $_GET['format'] ?? 'json';
$questionBank = new QuestionBank();
$questions = $questionBank->export();

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="question_bank_' . date('Y-m-d') . '.csv"');

    // UTF-8 BOM
    echo "\xEF\xBB\xBF";

    // CSV内容
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', '时间', '类型', '问题', '选项', '答案', '访问次数']);

    foreach ($questions as $q) {
        $types = [
            'single' => '单选',
            'multiple' => '多选',
            'judgement' => '判断',
            'completion' => '填空'
        ];

        fputcsv($output, [
            $q['id'],
            $q['created_at'],
            $types[$q['type']] ?? $q['type'],
            $q['question'],
            $q['options'],
            $q['answer'],
            $q['access_count']
        ]);
    }

    fclose($output);
} else {
    // JSON格式
    header('Content-Disposition: attachment; filename="question_bank_' . date('Y-m-d') . '.json"');
    echo json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>