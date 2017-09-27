<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'class' => Document::class,
            ]
        );
    }
}
