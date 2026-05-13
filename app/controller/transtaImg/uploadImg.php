<?php
// app/controller/ProductController.php
class ProductController
{
    private $uploadDir;

    public function __construct($uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?: __DIR__ . '/../../../uploads/';
    }

    public function getProducts()
    {
        if (!is_dir($this->uploadDir)) {
            return ['error' => 'Pasta de uploads não encontrada'];
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $arquivos = scandir($this->uploadDir);
        $produtos = [];

        foreach ($arquivos as $arquivo) {
            $caminhoCompleto = $this->uploadDir . $arquivo;
            if (!is_file($caminhoCompleto)) continue;

            $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
            if (!in_array($extensao, $extensoesPermitidas)) continue;

            $nomeSemExt = pathinfo($arquivo, PATHINFO_FILENAME);
            $nomeProduto = '';
            $preco = 0.0;

            // mesmo regex que você já tinha
            if (preg_match('/^(.*?)\.r(\d+(?:\.\d+)?)$/i', $nomeSemExt, $matches)) {
                $nomeProduto = trim($matches[1]);
                $preco = (float) $matches[2];
            } elseif (preg_match('/^(.*?)\.(\d+(?:\.\d+)?)$/', $nomeSemExt, $matches)) {
                $nomeProduto = trim($matches[1]);
                $preco = (float) $matches[2];
            } else {
                $nomeProduto = $nomeSemExt;
                $preco = 0.0;
            }

            if (empty($nomeProduto)) $nomeProduto = 'Produto sem nome';
            $nomeProduto = str_replace('_', ' ', $nomeProduto);
            $nomeProduto = mb_convert_case($nomeProduto, MB_CASE_TITLE, 'UTF-8');

            $produtos[] = [
                'nome' => $nomeProduto,
                'preco' => $preco,
                'precoFormatado' => 'R$ ' . number_format($preco, 2, ',', '.'),
                'imagem' => '/uploads/' . rawurlencode($arquivo),
                'arquivo' => $arquivo
            ];
        }

        usort($produtos, fn($a, $b) => strcmp($a['nome'], $b['nome']));
        return $produtos;
    }

    public function outputJson()
    {
        header('Content-Type: application/json; charset=utf-8');
        $result = $this->getProducts();
        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode($result);
        } else {
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        exit; // importante para não continuar a execução do roteador
    }
}
