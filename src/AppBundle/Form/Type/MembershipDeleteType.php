<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Membership;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Membership::class,
                'translation_domain' => 'member',
            ]
        );
    }
}
