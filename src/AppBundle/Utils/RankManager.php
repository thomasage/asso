<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Rank;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RankManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $imageDirectory;

    /**
     * @param EntityManager $em
     * @param string $imageDirectory
     */
    public function __construct(EntityManager $em, $imageDirectory)
    {
        $this->em = $em;
        $this->imageDirectory = $imageDirectory;
    }

    /**
     * @param Rank $rank
     * @param UploadedFile $image
     */
    public function updateImage(Rank $rank, UploadedFile $image)
    {
        $this->deleteImage($rank);
        $extension = $image->guessExtension();
        if ($image->move($this->imageDirectory, $rank->getId().'.'.$extension)) {
            $rank->setImageExtension($extension);
            $this->updateRank($rank);
        }
    }

    /**
     * @param Rank $rank
     */
    public function deleteImage(Rank $rank)
    {
        $files = glob($this->imageDirectory.'/'.$rank->getId().'.*');
        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        $rank->setImageExtension(null);
        $this->updateRank($rank);
    }

    /**
     * @param Rank $rank
     */
    public function updateRank(Rank $rank)
    {
        if ($rank->getPosition() == 99999) {
            $last = $this->em->getRepository('AppBundle:Rank')->findOneBy(array(), array('position' => 'DESC'));
            if ($last instanceof Rank) {
                $rank->setPosition($last->getPosition() + 1);
            } else {
                $rank->setPosition(0);
            }
        }

        $this->em->persist($rank);
        $this->em->flush();
    }

    /**
     * @param Rank $rank
     * @return null|string
     */
    public function getImage(Rank $rank)
    {
        $filename = $this->imageDirectory.'/'.$rank->getId().'.'.$rank->getImageExtension();
        if (file_exists($filename) && is_readable($filename)) {
            return $filename;
        }

        return null;
    }
}
