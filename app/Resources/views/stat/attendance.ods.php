<?php
use AppBundle\Entity\Lesson;
use AppBundle\Entity\Member;
use Box\Spout\Common\Type;
use Box\Spout\Writer\ODS\Writer;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;
use Symfony\Component\Translation\DataCollectorTranslator;

if (!isset($translator) || !$translator instanceof DataCollectorTranslator) {
    throw new Exception('Unable to find translator.');
}

if (!isset($lessons) || !is_array($lessons)) {
    throw new Exception('Unable to find lessons.');
}

$writer = WriterFactory::create(Type::ODS);
if (!$writer instanceof Writer) {
    exit();
}
$writer->openToBrowser('document.ods');

$styleHeader = (new StyleBuilder())
    ->setBackgroundColor(Color::BLACK)
    ->setFontBold()
    ->setFontColor(Color::WHITE)
    ->setShouldWrapText()
    ->build();

$styleNormal = (new StyleBuilder())
    ->setShouldWrapText()
    ->build();

$data = [
    'Jour',
    'Date',
    'Début',
    'Fin',
    'Niveau',
    'Membres',
];

$writer->addRowWithStyle($data, $styleHeader);

foreach ($lessons as $lesson) {

    if (!$lesson instanceof Lesson) {
        continue;
    }

    $members = [];
    foreach ($lesson->getMembers() as $m) {
        if (!$m instanceof Member) {
            continue;
        }
        $members[] = $m->__toString();
    }

    $data = [
        $translator->trans('weekday.'.$lesson->getDate()->format('l'), [], 'stat'),
        $lesson->getDate()->format('Y-m-d'),
        $lesson->getStart()->format('H:i'),
        $lesson->getStart()->modify('+'.$lesson->getDuration().' minutes')->format('H:i'),
        $lesson->getLevel()->getName(),
        implode("\n", $members),
    ];

    $writer->addRowWithStyle($data, $styleNormal);

}

$writer->close();
