<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
        $config = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '/../../config/config.yml'));
        $content = file_get_contents($config['main']['remote_url']);

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

        $fs = new Filesystem();

        $output->writeln('Found ' . count($allCores) . 'cores');
        foreach ($allCores as $allCore) {
            $destination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $allCore;
            $origin = $config['main']['remote_url'] . DIRECTORY_SEPARATOR . $allCore;
            $output->writeln('Downloading ' . $origin . ' to ' . $destination);
            file_put_contents($destination, file_get_contents($origin));

            // Decompress file
            $zip = new \ZipArchive();
            $res = $zip->open($destination);
            if ($res === TRUE) {
                $zip->extractTo($config['main']['dest_dir']);
                $zip->close();
                $fs->remove($destination);
                $output->writeln('Extracted ' . $destination);
            }
        }
    }
}