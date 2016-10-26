<?php
use AppBundle\Entity\Member;
use AppBundle\Entity\Rank;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;

$writer = WriterFactory::create(Type::ODS);
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

$writer->addRowWithStyle(
    [
        'Nom',
        'Prénom',
        'Date de naissance',
        'Adresse',
        'Code postal',
        'Ville',
        'Téléphone',
        'Téléphone',
        'Téléphone',
        'Téléphone',
        'E-mail',
        'E-mail',
        'Titre',
    ],
    $styleHeader
);

if (isset($members)) {

    foreach ($members as $member) {

        if (!$member instanceof Member) {
            continue;
        }

        $memberships = $member->getMemberships();
        $promotions = $member->getPromotions();

        $data = [
            $member->getLastname(),
            $member->getFirstname(),
            $member->getBirthday()->format('Y-m-d'),
            $member->getAddress(),
            $member->getZip(),
            $member->getCity(),
            $member->getPhone0(),
            $member->getPhone1(),
            $member->getPhone2(),
            $member->getPhone3(),
            $member->getEmail0(),
            $member->getEmail1(),
        ];

        if (count($promotions) > 0) {
            $promotion = $promotions[count($promotions) - 1];
            $rank = $promotion->getRank();
            if (!$rank instanceof Rank) {
                continue;
            }
            $data[] = $rank->getName()."\n".$rank->getDescription();
        }

        $writer->addRowWithStyle($data, $styleNormal);

    }

}

$writer->close();
