<?php

namespace Fardus\Bundle\CrudBundle\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Fardus\Bundle\CrudBundle\Generator\FardusCrudGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;

class FardusCrudCommand extends GenerateDoctrineCrudCommand
{

    protected function configure()
    {
        parent::configure();
        $this->setName('fardus:generate:crud');
    }

    protected function getGenerator(BundleInterface $bundle = null)
    {
        $generator = new FardusCrudGenerator($this->getContainer()->get('filesystem') );
        $generator->setSkeletonDirs(__DIR__ . '/../Resources/skeleton');
        $this->setGenerator($generator);
        return parent::getGenerator($bundle);
    }


    protected function createGenerator($bundle = null)
    {
        return new FardusCrudGenerator($this->getContainer()->get('filesystem'));
    }

}
