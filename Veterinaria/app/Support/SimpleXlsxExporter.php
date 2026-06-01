<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SimpleXlsxExporter
{
    public static function download(string $filename, array $headers, array $rows, string $sheetName = 'Sheet1'): BinaryFileResponse
    {
        $archivoTemporal = self::crearArchivo($headers, $rows, $sheetName);

        return response()->download($archivoTemporal, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private static function crearArchivo(array $headers, array $rows, string $sheetName): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('ZipArchive no está disponible en el servidor.');
        }

        $baseTemporal = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($baseTemporal === false) {
            throw new \RuntimeException('No se pudo crear el archivo temporal.');
        }

        $archivoXlsx = $baseTemporal . '.xlsx';
        rename($baseTemporal, $archivoXlsx);

        $zip = new ZipArchive();
        if ($zip->open($archivoXlsx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No se pudo crear el archivo XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', self::contenidoTypesXml());
        $zip->addFromString('_rels/.rels', self::contenidoRelsXml());
        $zip->addFromString('xl/workbook.xml', self::contenidoWorkbookXml($sheetName));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::contenidoWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', self::contenidoStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::contenidoSheetXml($headers, $rows));
        $zip->close();

        return $archivoXlsx;
    }

    private static function contenidoTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private static function contenidoRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function contenidoWorkbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::escaparXml($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private static function contenidoWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function contenidoStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    private static function contenidoSheetXml(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetData>';

        $allRows = array_merge([$headers], $rows);

        foreach ($allRows as $rowIndex => $row) {
            $numeroFila = $rowIndex + 1;
            $xml .= '<row r="' . $numeroFila . '">';

            foreach ($row as $columnIndex => $value) {
                $cellReference = self::columnaExcel($columnIndex) . $numeroFila;
                $xml .= '<c r="' . $cellReference . '" t="inlineStr"><is><t xml:space="preserve">'
                    . self::escaparXml((string) $value)
                    . '</t></is></c>';
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private static function columnaExcel(int $indice): string
    {
        $indice++;
        $columna = '';

        while ($indice > 0) {
            $modulo = ($indice - 1) % 26;
            $columna = chr(65 + $modulo) . $columna;
            $indice = intdiv($indice - 1, 26);
        }

        return $columna;
    }

    private static function escaparXml(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
