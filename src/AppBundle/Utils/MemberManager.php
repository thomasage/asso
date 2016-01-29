<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Promotion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MemberManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $photoDirectory;

    /**
     * @param EntityManager $em
     * @param string $photoDirectory
     */
    public function __construct(EntityManager $em, $photoDirectory)
    {
        $this->em = $em;
        $this->photoDirectory = $photoDirectory;
    }

    /**
     * @param Member $member
     * @param UploadedFile $photo
     */
    public function updatePhoto(Member $member, UploadedFile $photo)
    {
        $this->deletePhoto($member);
        $extension = $photo->guessExtension();
        if ($photo->move($this->photoDirectory, $member->getId().'.'.$extension)) {
            $member->setPhotoExtension($extension);
            $this->update($member);
        }
    }

    /**
     * @param Member $member
     */
    public function deletePhoto(Member $member)
    {
        $files = glob($this->photoDirectory.'/'.$member->getId().'.*');
        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        $member->setPhotoExtension(null);
        $this->update($member);
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
     * @return null|string
     */
    public function getPhoto(Member $member)
    {
        $filename = $this->photoDirectory.'/'.$member->getId().'.'.$member->getPhotoExtension();
        if (file_exists($filename) && is_readable($filename)) {
            return $filename;
        }

        return null;
    }

    /**
     * @param Member $member
     * @return \AppBundle\Entity\Promotion[]
     */
    public function getPromotions(Member $member)
    {
        return $this->em
            ->getRepository('AppBundle:Promotion')
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

    public function getNextBirthdays()
    {
        return $this->em->getRepository('AppBundle:Member')->findNextBirthdays();
    }

    /**
     * @param Member $member
     * @return \AppBundle\Entity\Membership[]
     */
    public function getMemberships(Member $member)
    {
        return $this->em
            ->getRepository('AppBundle:Membership')
            ->findBy(array('member' => $member), array('season' => 'DESC'));
    }

    /**
     * @param Membership $membership
     */
    public function updateMembership(Membership $membership)
    {
        $this->em->persist($membership);
        $this->em->flush();
    }

    /**
     * @param Membership $membership
     */
    public function deleteMembership(Membership $membership)
    {
        $this->em->remove($membership);
        $this->em->flush();
    }
}