<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Lesson::class,
                'translation_domain' => 'lesson',
            ]
        );
    }
}
