<?php
use AppBundle\Entity\Attendance;
use AppBundle\Entity\Lesson;
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
    $translator->trans('field.weekday', [], 'stat'),
    $translator->trans('field.date', [], 'stat'),
    $translator->trans('field.start', [], 'stat'),
    $translator->trans('field.stop', [], 'stat'),
    $translator->trans('field.level', [], 'stat'),
    $translator->trans('field.themes', [], 'stat'),
    $translator->trans('field.present', [], 'stat'),
    $translator->trans('field.apologize', [], 'stat'),
    $translator->trans('field.comment', [], 'stat'),
];

$writer->addRowWithStyle($data, $styleHeader);

foreach ($lessons as $lesson) {

    if (!$lesson instanceof Lesson) {
        continue;
    }

    $themes = [];
    foreach ($lesson->getThemes() as $t) {
        $themes[] = $t->__toString();
    }

    $apologize = $present = [];
    foreach ($lesson->getAttendances() as $a) {
        if (!$a instanceof Attendance) {
            continue;
        }
        if ($a->getState() == 1) {
            $apologize[] = $a->getMember()->__toString();
        } elseif ($a->getState() == 2) {
            $present[] = $a->getMember()->__toString();
        }
    }

    $data = [
        $translator->trans('weekday.'.$lesson->getDate()->format('l'), [], 'stat'),
        $lesson->getDate()->format('Y-m-d'),
        $lesson->getStart()->format('H:i'),
        $lesson->getStart()->modify('+'.$lesson->getDuration().' minutes')->format('H:i'),
        $lesson->getLevel()->getName(),
        implode("\n", $themes),
        implode("\n", $present),
        implode("\n", $apologize),
        $lesson->getComment(),
    ];

    $writer->addRowWithStyle($data, $styleNormal);

}

$writer->close();
