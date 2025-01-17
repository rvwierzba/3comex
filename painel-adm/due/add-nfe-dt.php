<?php

session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nfe_file'])) {
        // Verificar se o arquivo é um XML
        if (pathinfo($_FILES['nfe_file']['name'], PATHINFO_EXTENSION) !== 'xml') {
            throw new Exception('Apenas arquivos XML são permitidos.');
        }

        // Carregar o XML
        $xml = simplexml_load_file($_FILES['nfe_file']['tmp_name']);
        
        if ($xml === false) {
            throw new Exception('Falha ao carregar o XML.');
        }

        // Verificar a estrutura correta do XML
        $nfe = $xml->NFe;
        if (!$nfe) {
            throw new Exception('Estrutura do XML inválida ou incompleta.');
        }

        $infNFe = $nfe->infNFe;
        if (!$infNFe) {
            throw new Exception('Estrutura do XML inválida ou incompleta.');
        }

        $ide = $infNFe->ide;
        $emit = $infNFe->emit;
        $dest = $infNFe->dest;
        $total = $infNFe->total->ICMSTot;
        $produtos = $infNFe->det;

        if (!$ide || !$emit || !$dest || !$total) {
            throw new Exception('Estrutura do XML inválida ou incompleta.');
        }

        $chaveAcesso = str_replace('NFe', '', (string)$infNFe['Id']);

        $dadosNFe = [
            'CodBarra' => $chaveAcesso,
            'NatOper' => (string)$ide->natOp,
            'nNF' => (string)$ide->nNF,
            'Serie' => (string)$ide->serie,
            'DtEmissao' => (string)$ide->dhEmi,
            'EmitCNPJ' => (string)$emit->CNPJ,
            'EmitNome' => (string)$emit->xNome,
            'DestNome' => (string)$dest->xNome,
            'DestCNPJ' => (string)$dest->CNPJ,
            'ValorTotNota' => (string)$total->vNF,
            'produtos' => []
        ];

        foreach ($produtos as $produto) {
            $dadosNFe['produtos'][] = [
                'ItemCodigo' => (string)$produto->prod->cProd,
                'ItemDescricao' => (string)$produto->prod->xProd,
                'ItemQuantidade' => (string)$produto->prod->qCom,
                'ItemValor' => (string)$produto->prod->vProd,
                'ItemNCM' => (string)$produto->prod->NCM,
            ];
        }

        // Armazenar a NFe na sessão
        if (!isset($_SESSION['nfe_list'])) {
            $_SESSION['nfe_list'] = [];
        }
        $_SESSION['nfe_list'][] = $dadosNFe;

        // Retornar os dados da NFe para o frontend em JSON
        echo json_encode([
            'nNF' => $dadosNFe['nNF'],
            'chNFe' => $dadosNFe['CodBarra'],
            'dadosNFe' => $dadosNFe
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao processar o arquivo: ' . $e->getMessage()]);
    exit;
}

?>