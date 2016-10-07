<?php
use AppBundle\Entity\Member;
use fpdf\FPDF_EXTENDED;

class MemberPDF extends FPDF_EXTENDED
{
    public function __construct()
    {
        parent::__construct(15, 'L', 'mm', 'A4');
        $this->AliasNbPages();
        $this->SetAutoPageBreak(true, 20);
    }
}

$pdf = new MemberPDF();

$pdf->AddPage();

if (isset($members)) {

    foreach ($members as $member) {

        if (!$member instanceof Member) {
            continue;
        }

        $promotions = $member->getPromotions();

        $startY = $pdf->GetY();
        $lastY = $pdf->GetY();

        $pdf->SetFont('arial', 'B', 10);
        $pdf->MultiCell(45, 5, $member."\n".$member->getBirthday()->format('Y-m-d'), 0, 'L', false);
        $lastY = max($lastY, $pdf->GetY());

        $pdf->SetFont('arial', '', 10);

        $pdf->SetXY(60, $startY);
        $pdf->MultiCell(50, 5, $member->getAddress()."\n".$member->getZip().' '.$member->getCity(), 0, 'L', false);
        $lastY = max($lastY, $pdf->GetY());

        $pdf->SetY($startY);
        if (strlen($member->getPhone0()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getPhone0(), 0, 'L', false);
        }
        if (strlen($member->getPhone1()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getPhone1(), 0, 'L', false);
        }
        if (strlen($member->getPhone2()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getPhone2(), 0, 'L', false);
        }
        if (strlen($member->getPhone3()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getPhone3(), 0, 'L', false);
        }
        if (strlen($member->getEmail0()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getEmail0(), 0, 'L', false);
        }
        if (strlen($member->getEmail1()) > 0) {
            $pdf->SetX(110);
            $pdf->MultiCell(40, 5, $member->getEmail1(), 0, 'L', false);
        }
        $lastY = max($lastY, $pdf->GetY());

        if (count($promotions) > 0) {
            $promotion = $promotions[count($promotions) - 1];
            $pdf->SetXY(150, $startY);
            $pdf->MultiCell(40, 5, $promotion->getRank()."\n".$promotion->getRank()->getDescription(), 0, 'L', false);
            $lastY = max($lastY, $pdf->GetY());
        }

        $pdf->SetY($lastY + 5);

    }

}

$pdf->Output('Document.pdf', 'D');
