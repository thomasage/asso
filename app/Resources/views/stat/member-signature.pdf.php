<?php
use AppBundle\Entity\Member;
use fpdf\FPDF_EXTENDED;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

if (!isset($applicationName) || !is_string($applicationName)) {
    throw new LogicException('Unable to find application name.');
}

if (!isset($documentTitle) || !is_string($documentTitle)) {
    throw new LogicException('Unable to find document title.');
}

if (!isset($view) || !$view instanceof TimedPhpEngine) {
    throw new LogicException('Unable to find view.');
}

$translator = $view->get('translator');
if (!$translator instanceof TranslatorHelper) {
    throw new LogicException('Unable to find translator.');
}

class MemberSignaturePDF extends FPDF_EXTENDED
{
    /**
     * @var string
     */
    private $applicationName;

    /**
     * @var string
     */
    private $documentTitle;

    /**
     * @var TranslatorHelper
     */
    private $translator;

    public function __construct()
    {
        parent::__construct(15, 'P', 'mm', 'A4');
        $this->AliasNbPages();
    }

    public function Header()
    {
        $this->SetFont('arial', 'B', 12);
        $this->SetY(10);
        $this->MultiCell(0, 5, $this->applicationName, 0, 'C', false);

        $this->Ln(1);
        $this->SetFont('arial', 'B', 10);
        $this->MultiCell(0, 5, $this->documentTitle, 0, 'C', false);

        $this->Ln(5);
        $this->Cell(45, 7, $this->translator->trans('field.lastname', [], 'stat'), 1, 0, 'L', false);
        $this->Cell(45, 7, $this->translator->trans('field.firstname', [], 'stat'), 1, 0, 'L', false);
        $this->Cell(45, 7, $this->translator->trans('field.function', [], 'stat'), 1, 0, 'L', false);
        $this->Cell(40, 7, $this->translator->trans('field.signature', [], 'stat'), 1, 1, 'L', false);
    }

    public function Footer()
    {
        $this->SetFont('arial', '', 7);
        $this->SetY(-15);
        $this->MultiCell(0, 5, $this->applicationName, 0, 'L', false);
        $this->SetY(-15);
        $this->MultiCell(0, 5, 'Page '.$this->page.' sur {nb}', 0, 'R', false);
    }

    /**
     * @param string $applicationName
     * @return MemberSignaturePDF
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = (string)$applicationName;

        return $this;
    }

    /**
     * @param string $documentTitle
     * @return MemberSignaturePDF
     */
    public function setDocumentTitle($documentTitle)
    {
        $this->documentTitle = (string)$documentTitle;

        return $this;
    }

    /**
     * @param TranslatorHelper $translator
     * @return MemberSignaturePDF
     */
    public function setTranslator(TranslatorHelper $translator)
    {
        $this->translator = $translator;

        return $this;
    }
}

$pdf = new MemberSignaturePDF();
$pdf->setApplicationName($applicationName)
    ->setDocumentTitle($documentTitle)
    ->setTranslator($translator)
    ->SetAutoPageBreak(true, 20);

$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.1);

$pdf->AddPage();

if (isset($results) && is_array($results)) {

    foreach ($results as $result) {

        if (!$result instanceof Member) {
            continue;
        }

        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(45, 7, $result->getLastname(), 'LT', 0, 'L', false);
        $pdf->Cell(45, 7, $result->getFirstname(), 'T', 0, 'L', false);
        if ($result->getAge() < 18) {
            $pdf->Cell(45, 7, $translator->trans('member_under_age', [], 'stat'), 'RT', 0, 'L', false);
        } else {
            $pdf->Cell(45, 7, $translator->trans('member_over_age', [], 'stat'), 'RT', 0, 'L', false);
        }
        $pdf->Cell(40, 7, '', 'LTR', 1, 'L', false);
        if ($result->getAge() < 18) {
            $pdf->SetFont('arial', 'I', 10);
            if ($result->getGender() == 'f') {
                $pdf->Cell(135, 5, $translator->trans('female_represented_by', [], 'stat').' :', 'L', 0, 'L', false);
            } else {
                $pdf->Cell(135, 5, $translator->trans('male_represented_by', [], 'stat').' :', 'L', 0, 'L', false);
            }
        } else {
            $pdf->Cell(135, 5, '', 'L', 0, 'L', false);
        }
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(40, 5, '', 'LR', 1, 'L', false);
        $pdf->Cell(135, 5, '', 'BLR', 0, 'L', false);
        $pdf->Cell(40, 5, '', 'BLR', 1, 'L', false);

    }

}

$pdf->Output('Signature.pdf', 'D');
