<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Document;
use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MemberManager
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     * @param DocumentManager $dm
     */
    public function __construct(EntityManager $em, DocumentManager $dm)
    {
        $this->dm = $dm;
        $this->em = $em;
    }

    /**
     * @param Member $member
     */
    public function update(Member $member)
    {
        $this->em->persist($member);
        $this->em->flush();
    }

    /**
     * @param Member $member
     * @return \AppBundle\Entity\Promotion[]
     */
    public function getPromotions(Member $member)
    {
        return $this->em
            ->getRepository(Promotion::class)
            ->findBy(array('member' => $member), array('date' => 'ASC'));
    }

    /**
     * @param Promotion $promotion
     */
    public function updatePromotion(Promotion $promotion)
    {
        $this->em->persist($promotion);
        $this->em->flush();
    }

    /**
     * @param Promotion $promotion
     */
    public function deletePromotion(Promotion $promotion)
    {
        $this->em->remove($promotion);
        $this->em->flush();
    }

    /**
     * @param Season $season
     * @return \AppBundle\Entity\Member[]
     */
    public function getNextBirthdays(Season $season)
    {
        return $this->em->getRepository(Member::class)->findNextBirthdays($season);
    }

    /**
     * @param Member $member
     * @return \AppBundle\Entity\Membership[]
     */
    public function getMemberships(Member $member)
    {
        return $this->em
            ->getRepository(Membership::class)
            ->findBy(array('member' => $member), array('season' => 'DESC'));
    }

    /**
     * @param Membership $membership
     * @param Form $form
     */
    public function updateMembership(Membership $membership, Form $form)
    {
        $this->em->persist($membership);
        $this->em->flush();

        $medicalCertificate = $form->get('medicalCertificate')->getData();
        if ($medicalCertificate instanceof UploadedFile) {

            // Remove old document
            if (($d = $membership->getMedicalCertificate()) instanceof Document) {
                $membership->setMedicalCertificate(null);
                $this->em->flush();
                $this->dm->delete($d);
            }

            // Add new document
            $document = $this->dm->add($medicalCertificate);
            $membership->setMedicalCertificate($document);
            $this->em->flush();
        }

        $registrationForm = $form->get('registrationForm')->getData();
        if ($registrationForm instanceof UploadedFile) {

            // Remove old document
            if (($d = $membership->getRegistrationForm()) instanceof Document) {
                $membership->setRegistrationForm(null);
                $this->em->flush();
                $this->dm->delete($d);
            }

            // Add new document
            $document = $this->dm->add($registrationForm);
            $membership->setRegistrationForm($document);
            $this->em->flush();
        }
    }

    /**
     * @param Membership $membership
     */
    public function deleteMembership(Membership $membership)
    {
        $this->em->remove($membership);
        $this->em->flush();
    }

    /**
     * @param \DateTime $date
     * @return Member[]
     */
    public function findByDateGroupByLevel(\DateTime $date)
    {
        $season = $this->em->getRepository(Season::class)->findByDate($date);

        if (!$season instanceof Season) {
            return array();
        }

        $members = array();
        foreach ($this->em->getRepository(Level::class)->findAll() as $level) {
            $members[$level->getId()] = $this->em
                ->getRepository(Member::class)
                ->findByLevelAndSeason($level, $season);
        }

        return $members;
    }
}
