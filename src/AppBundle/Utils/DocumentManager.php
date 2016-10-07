<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Document;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentManager
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     * @param string $directory
     */
    public function __construct(EntityManager $em, $directory)
    {
        $this->directory = $directory;
        $this->em = $em;
    }

    /**
     * @param UploadedFile $file
     * @return Document
     */
    public function add(UploadedFile $file)
    {
        $document = new Document();
        $document
            ->setExtension($file->guessExtension())
            ->setMime($file->getMimeType())
            ->setName($file->getClientOriginalName())
            ->setSize($file->getSize());
        if (is_null($document->getExtension())) {
            $document->setExtension($file->getClientOriginalExtension());
        }

        $this->em->persist($document);
        $this->em->flush();

        $file->move(
            $this->directory.'/'.substr($document->getId(), 0, 1),
            $document->getId().'.'.$document->getExtension()
        );

        return $document;
    }

    /**
     * @param Document $document
     * @return bool
     */
    public function delete(Document $document)
    {
        $filename = $this->getFilename($document);

        $this->em->remove($document);
        $this->em->flush();

        if (file_exists($filename)) {
            unlink($filename);
        }

        return true;
    }

    /**
     * @param Document $document
     * @return string
     */
    public function getFilename(Document $document)
    {
        return $this->directory.'/'.substr($document->getId(), 0, 1).'/'.$document->getId().'.'
        .$document->getExtension();
    }
}
