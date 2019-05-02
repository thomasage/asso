<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\Season;
use DateTime;
use Doctrine\ORM\EntityManager;

class MemberManager
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function update(Member $member): void
    {
        $this->em->persist($member);
        $this->em->flush();
    }

    /**
     * @param Member $member
     * @return Promotion[]
     */
    public function getPromotions(Member $member): array
    {
        return $this->em
            ->getRepository(Promotion::class)
            ->findBy(array('member' => $member), array('date' => 'ASC'));
    }

    public function updatePromotion(Promotion $promotion): void
    {
        $this->em->persist($promotion);
        $this->em->flush();
    }

    public function deletePromotion(Promotion $promotion): void
    {
        $this->em->remove($promotion);
        $this->em->flush();
    }

    /**
     * @param Season $season
     * @return Member[]
     */
    public function getNextBirthdays(Season $season): array
    {
        return $this->em->getRepository(Member::class)->findNextBirthdays($season);
    }

    /**
     * @param Member $member
     * @return Membership[]
     */
    public function getMemberships(Member $member): array
    {
        return $this->em
            ->getRepository(Membership::class)
            ->findBy(array('member' => $member), array('season' => 'DESC'));
    }

    public function updateMembership(Membership $membership): void
    {
        $this->em->persist($membership);
        $this->em->flush();
    }

    public function deleteMembership(Membership $membership): void
    {
        $this->em->remove($membership);
        $this->em->flush();
    }

    /**
     * @param DateTime $date
     * @return Member[]
     */
    public function findByDateGroupByLevel(DateTime $date): array
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
