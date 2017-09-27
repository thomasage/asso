<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Theme::class,
                'translation_domain' => 'param',
            ]
        );
    }
}
