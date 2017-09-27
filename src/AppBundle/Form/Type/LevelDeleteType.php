<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Level;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LevelDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Level::class,
                'translation_domain' => 'param',
            ]
        );
    }
}
