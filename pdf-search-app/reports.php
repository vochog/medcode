<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$keyword = strtolower($_GET['keyword'] ?? 'example');
$urls = file('pdf_urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$results = [];

foreach ($urls as $url) {
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $localPath = __DIR__ . "/temp/$filename";

    // Download if not cached
    if (!file_exists($localPath)) {
        file_put_contents($localPath, file_get_contents($url));
    }

    $parser = new Parser();
    $pdf = $parser->parseFile($localPath);
    $pages = $pdf->getPages();

    foreach ($pages as $index => $page) {
        $text = strtolower($page->getText());
        if (strpos($text, $keyword) !== false) {
            preg_match("/(.{0,100}$keyword.{0,100})/i", $text, $matches);
            $snippet = $matches[1] ?? '';
            $highlighted = preg_replace("/($keyword)/i", "<mark>$1</mark>", htmlspecialchars($snippet));

            $results[] = [
                'url' => $url,
                'page' => $index + 1,
                'snippet' => $highlighted,
                'keyword' => $keyword
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PDF Keyword Search Report</title>
    <style>
        body { font-family: sans-serif; }
        mark { background: yellow; }
    </style>
</head>
<body>
    <h1>Search Results for "<?php echo htmlspecialchars($keyword); ?>"</h1>
    <?php foreach ($results as $r): 
        $pdfUrl = urlencode($r['url']);
    ?>
        <div style="margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
            <strong>PDF URL:</strong> <a href="<?= $r['url'] ?>" target="_blank"><?= $r['url'] ?></a><br>
            <strong>Page:</strong> <?= $r['page'] ?><br>
            <strong>Snippet:</strong><br> <?= $r['snippet'] ?><br>
            <a href="viewer/viewer.html?file=<?= $pdfUrl ?>#page=<?= $r['page'] ?>&keyword=<?= urlencode($r['keyword']) ?>" target="_blank">
                View PDF at Match
            </a>
        </div>
    <?php endforeach; ?>
</body>
</html>