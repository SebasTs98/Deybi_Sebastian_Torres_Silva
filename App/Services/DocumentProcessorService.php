<?php

// Define el namespace donde se encuentra el servicio.
// Permite organizar la lógica dentro de la carpeta App\Services.
namespace App\Services;

// Importa la librería Smalot\PdfParser para leer y extraer texto de archivos PDF.
use Smalot\PdfParser\Parser;

// Clase encargada de procesar documentos según su formato.
// Su objetivo es extraer información relevante desde archivos PDF o XML.
class DocumentProcessorService
{
    /**
     * Método principal que recibe un archivo y determina cómo procesarlo
     * según su extensión (pdf o xml).
     *
     * @param UploadedFile $file Archivo cargado por el usuario.
     * @return array Datos extraídos o mensaje de error.
     */
    public function extractData($file)
    {
        // Obtiene la extensión original del archivo.
        $extension = $file->getClientOriginalExtension();

        // Si el archivo es PDF, llama al método processPdf.
        if ($extension === 'pdf') {
            return $this->processPdf($file);
        }

        // Si el archivo es XML, llama al método processXml.
        if ($extension === 'xml') {
            return $this->processXml($file);
        }

        // Retorna error si el formato no está soportado.
        return [
            'error' => 'Formato no soportado'
        ];
    }

    /**
     * Procesa archivos PDF.
     * Extrae texto del PDF y utiliza expresiones regulares (regex)
     * para encontrar datos específicos como NIT, número de contrato y valor.
     *
     * @param UploadedFile $file
     * @return array Datos extraídos del documento.
     */
    private function processPdf($file)
    {
        // Crea instancia del parser PDF.
        $parser = new Parser();

        // Lee el archivo PDF desde su ruta temporal.
        $pdf = $parser->parseFile($file->getPathName());

        // Extrae todo el texto del documento.
        $text = $pdf->getText();

        // TODO: Ajustar expresiones regulares según estructura real de los documentos.
        preg_match('/NIT:\s*(\d+)/', $text, $nit);
        preg_match('/Contrato:\s*(\S+)/', $text, $contract);
        preg_match('/Valor:\s*\$?([\d.,]+)/', $text, $value);

        // Retorna los datos encontrados o null si no existen.
        return [
            'nit' => $nit[1] ?? null,
            'contract_number' => $contract[1] ?? null,
            'amount' => $value[1] ?? null,
        ];
    }

    /**
     * Procesa archivos XML.
     * Convierte el contenido XML en un arreglo asociativo.
     *
     * @param UploadedFile $file
     * @return array Datos del XML convertidos a array.
     */
    private function processXml($file)
    {
        // Carga el archivo XML.
        $xml = simplexml_load_file($file->getPathName());

        // Convierte el objeto XML a JSON y luego a array para facilitar su uso.
        return json_decode(json_encode($xml), true);
    }
}
