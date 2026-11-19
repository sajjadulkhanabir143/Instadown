<?php
if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    http_response_code(400);
    echo "URL parameter is required";
    exit;
}

$url = trim($_GET['url']);

// শুধু Instagram ডোমেইন অ্যালাউ করা (সিকিউরিটির জন্য)
$host = parse_url($url, PHP_URL_HOST);
if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/(^|\.)(instagram\.com|instagr\.am)$/i', (string)$host)) {
    http_response_code(400);
    echo "Please enter a valid Instagram URL.";
    exit;
}

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
    'Accept-Language: en-US,en;q=0.9',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_ENCODING, '');
$html = curl_exec($ch);
curl_close($ch);

// Instagram পেজ থেকে JSON data পার্স করা
if ($html && preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches)) {
    $json = json_decode($matches[1], true);

    if (isset($json['contentUrl'])) {
        $mediaUrl = $json['contentUrl'];
        $isVideo = strpos($mediaUrl, '.mp4') !== false;
        $filename = $isVideo ? "starexx.mp4" : "starexx.jpg";
        $contentType = $isVideo ? "video/mp4" : "image/jpeg";

        $data = file_get_contents($mediaUrl);

        if ($data !== false) {
            header("Content-Type: $contentType");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            echo $data;
            exit;
        }
    }
}

http_response_code(404);
echo "Invalid or private Instagram post.";
