<?php
// clientes/inserir.php - TESTE DE ALCANCE "PING"

// Define que a resposta será texto plano (para o AJAX ler fácil)
header('Content-Type: text/plain; charset=utf-8');

$timestamp = date('Y-m-d H:i:s');
$output = "PING! O arquivo inserir.php FOI ALCANÇADO E EXECUTADO em: " . $timestamp . "\n\n";

// Adiciona alguns dados do POST para ver se chegam
$output .= "ID recebido (\$_POST['id']): " . htmlspecialchars($_POST['id'] ?? 'NENHUM ID') . "\n";
$output .= "NomeRes recebido (\$_POST['NomeRes']): " . htmlspecialchars($_POST['NomeRes'] ?? 'NENHUM NomeRes') . "\n";
// Adicione mais um ou dois campos que você espera do formulário para teste, se quiser.

// Log para o arquivo de erro do servidor (para termos uma segunda confirmação)
error_log("PING INSERIR.PHP EXECUTADO: " . $timestamp . " - ID: " . ($_POST['id'] ?? 'N/A'));

echo $output; // Envia a resposta
exit(); // Para o script aqui, não executa mais nada
?>