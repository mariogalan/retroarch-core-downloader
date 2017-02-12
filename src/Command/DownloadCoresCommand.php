<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by IntelliJ IDEA.
 * User: galan
 * Date: 11/02/17
 * Time: 21:03
 */
class DownloadCoresCommand extends Command
{
    protected function configure()
    {
        $this
          // the name of the command (the part after "bin/console")
          ->setName('download')

          // the short description shown while running "php bin/console list"
          ->setDescription('Download cores')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp("Probably what you want to do with this script")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'https://buildbot.libretro.com/nightly/linux/x86_64/latest/';
        $content = file_get_contents($url);

        $dom = new \DOMDocument();
        $dom->loadHTML($content);

        $allAnchors = $dom->getElementsByTagName('a');
        $allCores = [];
        foreach ($allAnchors as $item) {
            echo $item->nodeValue;
            if (strpos($item->nodeValue, '.zip')) {
                $allCores[] = $item->nodeValue;
            }
        }

        var_dump($allCores);
        foreach ($allCores as $allCore) {
            file_put_contents($allCore, file_get_contents($url . $allCore));
        }
    }

}