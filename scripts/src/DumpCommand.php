<?php

namespace LegoMedia\LegoLand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class DumpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump-res')
            ->addArgument('res', InputArgument::REQUIRED, '.res file');
    }
}
