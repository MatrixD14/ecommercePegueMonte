<?php
require_once 'Env.php';
Env::load();

// 1. Pega os dados da sua classe Env
$client_email = Env::get('google_drive', 'client_email');
$private_key  = str_replace('\n', "\n", Env::get('google_drive', 'private_key'));
$folder_id    = Env::get('google_drive', 'folder_id');

// 2. Função para gerar o Token (PHP Puro, sem biblioteca gorda)
function get_google_token($email, $key)
{
    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $payload = json_encode([
        'iss' => $email,
        'scope' => 'https://www.googleapis.com/auth/drive.file',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => time() + 3600,
        'iat' => time()
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $key, OPENSSL_ALGO_SHA256);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    $res = json_decode(curl_exec($ch), true);
    return $res['access_token'];
}

// 3. Executando o Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {

    $token = get_google_token($client_email, $private_key);

    // Sua ideia de colocar os dados no nome da imagem:
    $nomeItem = $_POST['nome_item'];
    $preco = $_POST['preco'];
    $nomeFinal = "ITEM_{$nomeItem}_PRECO_{$preco}_" . time() . ".jpg";

    $metadata = json_encode([
        'name' => $nomeFinal,
        'parents' => [$folder_id]
    ]);

    $content = file_get_contents($_FILES['imagem']['tmp_name']);
    $boundary = "---" . uniqid();

    $post_data = "--$boundary\r\n"
        . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
        . $metadata . "\r\n"
        . "--$boundary\r\n"
        . "Content-Type: image/jpeg\r\n\r\n"
        . $content . "\r\n"
        . "--$boundary--";

    $ch = curl_init("https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: multipart/related; boundary=$boundary"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $resposta = curl_exec($ch);
    curl_close($ch);

    echo "Pronto! Foto enviada com os dados no nome.";
}
