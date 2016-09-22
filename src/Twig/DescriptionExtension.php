<?php

namespace Psi\Bundle\Description\Twig;

use Psi\Component\Description\DescriptionFactory;
use Psi\Component\Description\Subject;

class DescriptionExtension extends \Twig_Extension
{
    private $descriptionFactory;

    public function __construct(DescriptionFactory $descriptionFactory)
    {
        $this->descriptionFactory = $descriptionFactory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('psi_describe', [$this, 'getDescription']),
        ];
    }

    public function getDescription($classOrObject)
    {
        $subject = $this->getSubject($classOrObject);

        return $this->descriptionFactory->describe($subject);
    }

    public function getName()
    {
        return 'psi_resource_description';
    }

    private function getSubject($classOrObject)
    {
        if (is_object($classOrObject)) {
            return Subject::createFromObject($classOrObject);
        }

        return Subject::createFromClass($classOrObject);
    }
}
