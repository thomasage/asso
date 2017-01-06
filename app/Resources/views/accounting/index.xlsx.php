<?php
use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionDetail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

if (!isset($categories) || !is_array($categories)) {
    $categories = [];
}
$countCategories = count($categories);

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$colnum = 6;
$sheet
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Date de valeur')
    ->setCellValue('C1', 'Mode')
    ->setCellValue('D1', 'Tiers')
    ->setCellValue('E1', 'Montant')
    ->setCellValue('F1', 'Solde');
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
foreach ($categories as $category) {
    $sheet->getColumnDimension(num2col($colnum))->setAutoSize(true);
    $sheet->setCellValue(num2col($colnum++).'1', $category);
}
$cells = $sheet->getStyle('A1:'.num2col($colnum).'1');
$cells->getFont()->setBold(true);
$cells->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

if (isset($transactions) && is_array($transactions)) {

    $rownum = 1;

    foreach ($transactions as $t => $transaction) {

        if (!$transaction instanceof Transaction) {
            continue;
        }

        $rownum++;

        $sheet->setCellValue('A'.$rownum, $transaction->getDate()->format('d/m/Y'));
        if ($transaction->getDateValue() instanceof DateTime) {
            $sheet->setCellValue('B'.$rownum, $transaction->getDateValue()->format('d/m/Y'));
        }
        $sheet->setCellValue('C'.$rownum, $transaction->getPaymentMethod()->getName());
        $sheet->setCellValue('D'.$rownum, $transaction->getThirdName());
        $sheet->setCellValue('E'.$rownum, $transaction->getAmount());
        if ($t > 0) {
            $sheet->setCellValue('F'.$rownum, '=E'.$rownum.'+F'.($rownum - 1));
        } else {
            $sheet->setCellValue('F'.$rownum, '=E'.$rownum);
        }
        foreach ($transaction->getDetails() as $detail) {
            if (!$detail instanceof TransactionDetail) {
                continue;
            }
            $index = array_search($detail->getCategory(), $categories, true) + 6;
            $sheet->setCellValue(num2col($index).$rownum, $detail->getAmount());
        }

    }

    $cells = $sheet->getStyle('A1:B'.$rownum);
    $cells
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
    $cells->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $cells = $sheet->getStyle('E1:'.num2col($colnum).$rownum);
    $cells
        ->getNumberFormat()
        ->setFormatCode('#,##0.00_-€;[Red]\-#,##0.00_-€');

}

// Render
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="document.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');
$writer = IOFactory::createWriter($spreadsheet, 'Excel2007');
$writer->save('php://output');
exit();

/**
 * @param int $num
 * @return string
 */
function num2col($num)
{
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = (int)($num / 26);
    if ($num2 > 0) {
        return num2col($num2 - 1).$letter;
    } else {
        return $letter;
    }
}
