<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class DocumentProcessorService
{
    public function extractData($file)
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'pdf') {
            return $this->processPdf($file);
        }

        if ($extension === 'xml') {
            return $this->processXml($file);
        }

        return [
            'error' => 'Formato no soportado'
        ];
    }

    private function processPdf($file)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathName());
        $text = $pdf->getText();

        // TODO: Ajustar regex a tus documentos reales
        preg_match('/NIT:\s*(\d+)/', $text, $nit);
        preg_match('/Contrato:\s*(\S+)/', $text, $contract);
        preg_match('/Valor:\s*\$?([\d.,]+)/', $text, $value);

        return [
            'nit' => $nit[1] ?? null,
            'contract_number' => $contract[1] ?? null,
            'amount' => $value[1] ?? null,
        ];
    }

    private function processXml($file)
    {
        $xml = simplexml_load_file($file->getPathName());

        return json_decode(json_encode($xml), true);
    }
}
