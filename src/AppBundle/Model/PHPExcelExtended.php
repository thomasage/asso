<?php
declare(strict_types=1);

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PHPExcelExtended extends \PHPExcel
{
    /**
     * @var \PHPExcel_Worksheet
     */
    private $sheet;

    public function __construct()
    {
        parent::__construct();

        $this->sheet = $this->getActiveSheet();
        $this->sheet->setTitle('Feuille 1');
    }

    /**
     * @param int $index
     * @return string
     */
    public static function colIndexToString(int $index): string
    {
        return \PHPExcel_Cell::stringFromColumnIndex($index);
    }

    /**
     * @param string $cells
     * @param array $format
     * @return bool
     */
    public function setCellFormat(string $cells, array $format): bool
    {
        try {

            if (isset($format['align'])) {
                if ($format['align'] === 'center') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getAlignment()
                        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                } elseif ($format['align'] === 'left') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getAlignment()
                        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                } elseif ($format['align'] === 'right') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getAlignment()
                        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }
            }

            if (isset($format['bold']) && $format['bold'] === true) {
                $this
                    ->sheet
                    ->getStyle($cells)
                    ->getFont()
                    ->setBold(true);
            }

            if (isset($format['borders']) && $format['borders'] === true) {
                $this
                    ->sheet
                    ->getStyle($cells)
                    ->applyFromArray(
                        [
                            'borders' => [
                                'allborders' => [
                                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                ],
                            ],
                        ]
                    );
            }

            if (isset($format['color'])) {
                $this
                    ->sheet
                    ->getStyle($cells)
                    ->getFont()
                    ->getColor()
                    ->setARGB('00'.$format['color']);
            }

            if (isset($format['fill'])) {
                $this
                    ->sheet
                    ->getStyle($cells)
                    ->applyFromArray(
                        [
                            'fill' => [
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => [
                                    'argb' => '00'.$format['fill'],
                                ],
                            ],
                        ]
                    );
            }

            if (isset($format['number'])) {
                if ($format['number'] === 'currency') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => '#,##0.00_-€']);
                } elseif ($format['number'] === 'date') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY]);
                } elseif ($format['number'] === 'datetime') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME]);
                } elseif ($format['number'] === 'float1') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => '0.0']);
                } elseif ($format['number'] === 'float2') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => '0.00']);
                } elseif ($format['number'] === 'integer') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER]);
                } elseif ($format['number'] === 'percent') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getNumberFormat()
                        ->applyFromArray(['code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00]);
                }
            }

            if (isset($format['valign'])) {
                if ($format['valign'] === 'center') {
                    $this
                        ->sheet
                        ->getStyle($cells)
                        ->getAlignment()
                        ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                }
            }

            if (isset($format['wrap']) && $format['wrap'] === true) {
                $this
                    ->sheet
                    ->getStyle($cells)
                    ->getAlignment()
                    ->setWrapText(true);
            }

            return true;

        } catch (\PHPExcel_Exception $e) {

            return false;

        }
    }

    /**
     * @param string $name
     * @return StreamedResponse
     */
    public function getStreamedResponse(string $name): StreamedResponse
    {
        $this->sheet = $this->setActiveSheetIndex();
        $this->sheet->setSelectedCell();

        $writer = new \PHPExcel_Writer_Excel2007($this);
        $writer->setPreCalculateFormulas(true);

        $response = new StreamedResponse();
        $response->setCallback(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param string $cell
     * @param mixed $value
     * @param mixed $type
     * @return PHPExcelExtended
     */
    public function setCellValue(string $cell, $value, $type = null): PHPExcelExtended
    {
        if ($type === 'text') {
            $this->sheet->setCellValueExplicit($cell, $value, \PHPExcel_Cell_DataType::TYPE_STRING);
        } else {
            $this->sheet->setCellValue($cell, $value);
        }

        return $this;
    }

    /**
     * @param string $cell
     */
    public function freezePane(string $cell): void
    {
        $this->sheet->freezePane($cell);
    }

    /**
     * @param string $cells
     */
    public function mergeCells(string $cells): void
    {
        $this->sheet->mergeCells($cells);
    }

    /**
     * @param array $cols
     * @return PHPExcelExtended
     */
    public function setAutoSize(array $cols): PHPExcelExtended
    {
        foreach ($cols as $col) {
            $this->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        return $this;
    }

    /**
     * @param string $col
     * @param float $size
     */
    public function setColumnWidth(string $col, float $size): void
    {
        $this->sheet->getColumnDimension($col)->setWidth($size);
    }

    /**
     * @param int $rownum
     * @param int $height
     */
    public function setRowHeight(int $rownum, int $height): void
    {
        $this->sheet->getRowDimension($rownum)->setRowHeight($height);
    }

    /**
     * @param string $title
     */
    public function addNewSheet(string $title): void
    {
        $this->sheet = $this->createSheet();
        $this->setTitle($title);
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->sheet->setTitle($title);
    }
}
