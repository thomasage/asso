<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Member;
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
}