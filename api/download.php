<?php
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo "URL parameter is required";
    exit;
}

$url = $_GET['url'];

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
    'Accept-Language: en-US,en;q=0.9',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
$html = curl_exec($ch);
curl_close($ch);

// Instagram পেজ থেকে JSON data পার্স করা
if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/', $html, $matches)) {
    $json = json_decode($matches[1], true);

    if (isset($json['contentUrl'])) {
        $mediaUrl = $json['contentUrl'];
        $filename = (strpos($mediaUrl, '.mp4') !== false) ? "starexx.mp4" : "starexx.jpg";
        $contentType = (strpos($mediaUrl, '.mp4') !== false) ? "video/mp4" : "image/jpeg";

        $data = file_get_contents($mediaUrl);

        header("Content-Type: $contentType");
        header("Content-Disposition: attachment; filename=$filename");
        echo $data;
        exit;
    }
}

// fallback: যখন JSON format না পাওয়া যায়
echo "Invalid or private Instagram post.";
