<?php

namespace Fardus\Bundle\CrudBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Fardus\Bundle\CrudBundle\Generator\FardusCrudGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;

/**
 * Class FardusCrudCommand
 * @package Fardus\Bundle\CrudBundle\Command
 */
class FardusCrudCommand extends GenerateDoctrineCrudCommand
{

    /**
     * configure
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('fardus:generate:crud');
    }

    /**
     * @param BundleInterface|null $bundle
     *
     * @return mixed
     */
    protected function getGenerator(BundleInterface $bundle = null)
    {
        /** @var FardusCrudGenerator $generator */
        $generator = $this->getContainer()->get('fardus_crud.generator');
        $generator->setSkeletonDirs(__DIR__ . '/../Resources/skeleton');
        $this->setGenerator($generator);

        return parent::getGenerator($bundle);
    }


    protected function createGenerator($bundle = null)
    {
        return new FardusCrudGenerator($this->getFilesystem(), '');
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->getContainer()->get('filesystem');
    }

}
