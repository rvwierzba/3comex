<?php

try {
    require_once(__DIR__ . '/../../../tcpdf/tcpdf_import.php'); // Ajuste o caminho conforme necessário

    // Definir o diretório de logs e PDF
    $logDir = __DIR__ . '/log';
    $pdfDir = __DIR__ . '/pdf';

    // Criar os diretórios se não existirem
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    if (!file_exists($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    // Função para formatar datas
    function formatDate($date) {
        $timestamp = strtotime($date);
        return date('d/m/Y', $timestamp);
    }

    // Verificar se há itens no POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json'); // Adicionar cabeçalho de JSON
        if (isset($_POST['numeroSolicitacao']) && isset($_POST['dataSolicitacao']) && isset($_POST['cliente']) &&
            isset($_POST['observacoes']) && isset($_POST['valorSolicitado']) && isset($_POST['formaPagamento']) &&
            isset($_POST['dataVencimento']) && isset($_POST['finalidade']) && isset($_POST['itens'])) {

            $numeroSolicitacao = $_POST['numeroSolicitacao'];
            $dataSolicitacao = formatDate($_POST['dataSolicitacao']);
            $cliente = $_POST['cliente'];
            $observacoes = $_POST['observacoes'];
            $valorSolicitado = $_POST['valorSolicitado'];
            $formaPagamento = $_POST['formaPagamento'];
            $dataVencimento = formatDate($_POST['dataVencimento']);
            $finalidade = $_POST['finalidade'];
            $itens = json_decode($_POST['itens'], true);

            // Adicionar logs para depuração
            file_put_contents($logDir . '/debug_full_post_' . $numeroSolicitacao . '.txt', print_r($_POST, true));
            file_put_contents($logDir . '/debug_valid_data_' . $numeroSolicitacao . '.txt', print_r([
                'numeroSolicitacao' => $numeroSolicitacao,
                'dataSolicitacao' => $dataSolicitacao,
                'cliente' => $cliente,
                'observacoes' => $observacoes,
                'valorSolicitado' => $valorSolicitado,
                'formaPagamento' => $formaPagamento,
                'dataVencimento' => $dataVencimento,
                'finalidade' => $finalidade,
                'itens' => $itens
            ], true));

            // Para depuração: exibir os dados recebidos
            file_put_contents($logDir . '/debug_items_' . $numeroSolicitacao . '.txt', print_r($itens, true));

            // Criar novo documento PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Configurações do documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('3 Comex');
            $pdf->SetTitle('Solicitação de Numerário');
            $pdf->SetSubject('Solicitação de Numerário');
            $pdf->SetKeywords('TCPDF, PDF, solicitação, numerário');

            // Adicionar página
            $pdf->AddPage();

            // Adicionar logo e informações do sistema
            $html = '<div style="text-align:left; display:flex; align-items:center;">';
            $html .= '<img src="../../../img/logo.png" alt="Logo" width="50" height="50" style="margin-right:10px;" />';
            $html .= '<h1 style="font-size:24px; line-height:50px; color:#333;">3 Comex</h1>';
            $html .= '</div>';
            $pdf->writeHTML($html, true, false, true, false, '');

            // Adicionar título
            $html = '<h2 style="color:#333;">Solicitação de Numerário</h2>';
            $pdf->writeHTML($html, true, false, true, false, '');

            // Adicionar número da solicitação e outros campos
            $html = '<p><strong>Número da Solicitação:</strong> ' . $numeroSolicitacao . '</p>';
            $html .= '<p><strong>Data da Solicitação:</strong> ' . $dataSolicitacao . '</p>';
            $html .= '<p><strong>Cliente:</strong> ' . $cliente . '</p>';
            $html .= '<p><strong>Observações:</strong> ' . $observacoes . '</p>';
            $html .= '<p><strong>Valor Solicitado:</strong> R$ ' . number_format($valorSolicitado, 2, ',', '.') . '</p>';
            $html .= '<p><strong>Forma de Pagamento:</strong> ' . $formaPagamento . '</p>';
            $html .= '<p><strong>Data de Vencimento:</strong> ' . $dataVencimento . '</p>';
            $html .= '<p><strong>Finalidade:</strong> ' . $finalidade . '</p>';
            $pdf->writeHTML($html, true, false, true, false, '');

            // Adicionar tabela de itens
            $html = '<h2 style="color:#333;">Itens da Solicitação</h2>';
            $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; width:100%;">';
            $html .= '<thead style="background-color:#f2f2f2;">';
            $html .= '<tr style="text-align:center;">
                        <th><strong>Descrição</strong></th>
                        <th><strong>Tributável</strong></th>
                        <th><strong>Valor (R$)</strong></th>
                        <th><strong>Total (R$)</strong></th>
                      </tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($itens as $item) {
                $descricao = trim($item['descricao']);
                $tributavel = $item['tributavel'];
                $valor = number_format((float)$item['valor'], 2, ',', '.');
                $total = number_format((float)$item['total'], 2, ',', '.');

                $html .= '<tr style="text-align:center;">
                            <td>' . $descricao . '</td>
                            <td>' . $tributavel . '</td>
                            <td>' . $valor . '</td>
                            <td>' . $total . '</td>
                          </tr>';
            }

            $html .= '</tbody></table>';
            $pdf->writeHTML($html, true, false, true, false, '');

            // Fechar e gerar PDF
            $pdfFilePath = $pdfDir . '/solicitacao_numerario_' . $numeroSolicitacao . '.pdf';
            $pdf->Output($pdfFilePath, 'F');

            // Retornar a URL do PDF gerado
            echo json_encode(['pdf_url' => '/3comex/TCPDF/telas/solicitacao_de_numerarios/pdf/solicitacao_numerario_' . $numeroSolicitacao . '.pdf']);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro: Nenhum dado recebido.']);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erro: Nenhum dado POST recebido.']);
        exit;
    }
} catch (Exception $e) {
    file_put_contents($logDir . '/error_' . $numeroSolicitacao . '.txt', $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
    exit;
}
?>
