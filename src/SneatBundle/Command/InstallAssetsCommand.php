<?php

namespace App\SneatBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'sneat:install-assets',
    description: 'Install Sneat admin theme assets',
)]
class InstallAssetsCommand extends Command
{
    private string $projectDir;
    
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();
        
        $bundleAssetsDir = $this->projectDir . '/src/SneatBundle/Resources/public';
        $publicAssetsDir = $this->projectDir . '/public/bundles/sneat';
        
        if (!$filesystem->exists($bundleAssetsDir)) {
            $io->error('Sneat assets not found in bundle directory.');
            return Command::FAILURE;
        }
        
        // Create directory if not exists
        if (!$filesystem->exists($publicAssetsDir)) {
            $filesystem->mkdir($publicAssetsDir);
        }
        
        // Copy assets 
        $io->section('Copying Sneat assets to public directory...');
        $filesystem->mirror($bundleAssetsDir, $publicAssetsDir, null, ['override' => true, 'delete' => true]);
        
        $io->success('Sneat assets have been successfully installed.');
        $io->note('You can now access the admin dashboard at /admin/dashboard');
        
        return Command::SUCCESS;
    }
} 