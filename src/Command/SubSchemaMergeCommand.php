<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Command;

use http\Exception\RuntimeException;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SubSchemaMergeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('avro:subschema:merge')
            ->setDescription('Merges subschema')
            ->setHelp('Merges all schema template files and creates schema files')
            ->addArgument('templateDirectory', InputArgument::REQUIRED, 'Schema template directory')
            ->addArgument('outputDirectory', InputArgument::REQUIRED, 'Output directory')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Merging schema files');

        $templateDirectory = $this->fixRelativePath($input->getArgument('templateDirectory'));
        $outputDirectory = $this->fixRelativePath($input->getArgument('outputDirectory'));

        $registry = (new SchemaRegistry())
            ->addSchemaTemplateDirectory($templateDirectory)
            ->load();

        $merger = SchemaMerger::create()
            ->setSchemaRegistry($registry)
            ->setOutputDirectory($outputDirectory);

        $result = $merger->merge();


        // retrieve the argument value using getArgument()
        $output->writeln(sprintf('Merged %d root schema files', $result));
    }

    /**
     * @param string $path
     * @return string
     */
    private function fixRelativePath(string $path): string
    {
        $result = $path;
        if (0 === strpos($path, './')) {
            $result = realpath($path);
        }

        if (false === $result || false === is_dir($result)) {
            throw new \RuntimeException(sprintf('Directory not found %s', $path));
        }

        return $result;
    }
}