<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Promotion::class,
                'translation_domain' => 'member',
            ]
        );
    }
}
