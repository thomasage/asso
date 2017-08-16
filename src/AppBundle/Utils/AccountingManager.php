<?php
declare(strict_types=1);

namespace AppBundle\Utils;

use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionDetail;
use AppBundle\Model\PHPExcelExtended;
use Symfony\Component\HttpFoundation\Response;

class AccountingManager
{
    /**
     * @param array $categories
     * @param Transaction[] $transactions
     * @return Response
     */
    public function export(array $categories, array $transactions): Response
    {
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr_FR', 'fr.UTF8', 'fr');

        $intl = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);

        try {

            $excel = new PHPExcelExtended();
            $excel->freezePane('A2');

            $excel->setAutoSize(['A', 'B', 'C', 'D', 'E', 'F']);

            $excel
                ->setCellValue('A1', 'Date')
                ->setCellValue('B1', 'Date de valeur')
                ->setCellValue('C1', 'Mode')
                ->setCellValue('D1', 'Tiers')
                ->setCellValue('E1', 'Montant')
                ->setCellValue('F1', 'Solde');
            $colnum = 5;
            foreach ($categories as $category) {
                $colnum++;
                $colstring = PHPExcelExtended::colIndexToString($colnum);
                $excel
                    ->setAutoSize([$colstring])
                    ->setCellValue($colstring.'1', $category);
            }
            $colstring = PHPExcelExtended::colIndexToString($colnum);
            $excel->setCellFormat(
                'A1:'.$colstring.'1',
                [
                    'align' => 'center',
                    'bold' => true,
                    'borders' => true,
                    'fill' => 'eeeeee',
                ]
            );

            $rownum = 1;

            foreach ($transactions as $t => $transaction) {

                if (!$transaction instanceof Transaction) {
                    continue;
                }

                $rownum++;

                $excel->setCellValue('A'.$rownum, $intl->format($transaction->getDate()));
                if ($transaction->getDateValue() instanceof \DateTime) {
                    $excel->setCellValue('B'.$rownum, $intl->format($transaction->getDateValue()));
                }
                $excel->setCellValue('C'.$rownum, $transaction->getPaymentMethod()->getName());
                $excel->setCellValue('D'.$rownum, $transaction->getThirdName());
                $excel->setCellValue('E'.$rownum, $transaction->getAmount());
                if ($t > 0) {
                    $excel->setCellValue('F'.$rownum, '=E'.$rownum.'+F'.($rownum - 1));
                } else {
                    $excel->setCellValue('F'.$rownum, '=E'.$rownum);
                }
                foreach ($transaction->getDetails() as $detail) {
                    if (!$detail instanceof TransactionDetail) {
                        continue;
                    }
                    $index = array_search($detail->getCategory(), $categories, true) + 6;
                    $excel->setCellValue(PHPExcelExtended::colIndexToString($index).$rownum, $detail->getAmount());
                }

            }

            $excel->setCellFormat('A1:B'.$rownum, ['align' => 'center', 'number' => 'date']);
            $excel->setCellFormat('E1:'.$colstring.$rownum, ['number' => 'currency']);

            return $excel->getStreamedResponse('Export.xlsx');

        } catch (\Exception $e) {

            return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }
}
