<?php
/**
 * Tinypng Optimize All
 *
 * @package Konatsu_TinypngOptimizeAll
 * @author Robert Rupa <robert@konatsu.pl>
 * @license OSL-3.0, AFL-3.0
 */

namespace Konatsu\TinypngOptimizeAll\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Tinify;
use Tinify\Magento\Model\Config as TinifyConfig;

class TinypngOptimizeAll extends Command
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Tinify\Magento\Model\Config
    */
    protected $tinifyConfig;

    /**
     * @var array
    */
    protected $_includeFiles = [
        'jpeg',
        'jpg',
        'png'
    ];
    
    /**
     * @var array
    */
    protected $_excludeDirs = [
        '(cache)',
        '(tmp)',
        '(.thumbs)'
    ];

    /**
     * Constructor of TinypngOptimizeAll
     *
     * @param Magento\Framework\Filesystem $_filesystem
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Tinify\Magento\Model\Config $tinifyConfig
     */
    public function __construct(
        Filesystem $_filesystem,
        ScopeConfig $scopeConfig,
        TinifyConfig $tinifyConfig
    ) {
        $this->_filesystem = $_filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->tinifyConfig = $tinifyConfig;
        parent::__construct();
    }

    private function hasKey()
    {
        return !empty($this->getKey());
    }

    protected function getKey()
    {
        return trim($this->scopeConfig->getValue($this->tinifyConfig::KEY_PATH));
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('konatsu:tinypng:optimize-all')
            ->setDescription('Regenerate Url rewrites of products/categories');
        
    }

    /**
     * Optimize all images
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($this->hasKey()){
            Tinify\setKey(trim($this->getKey()));
        }
        $output->writeln('<info>Start Optimize all images</info>');
        
        $dir = new \RecursiveDirectoryIterator(
            $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        
        $this->loopThrougDir($dir, $input, $output);
    }
    
    /**
     * loopThrougDir
     * @param  RecursiveDirectoryIterator $dir
     * @param  InputInterface             $input
     * @param  OutputInterface            $output
     * @return void
     */
    protected function loopThrougDir($dir, InputInterface $input, OutputInterface $output)
    {
        foreach ($dir as $fullPath => $fileinfo) {
            if ($fileinfo->isDir()){
                $this->loopThrougDir(
                    new \RecursiveDirectoryIterator(
                        $fileinfo->getPathName(),
                        \RecursiveDirectoryIterator::SKIP_DOTS
                    ),
                    $input,
                    $output
                );
            }

            if (preg_match('~\.('.implode('|',$this->_includeFiles).')$~', $fullPath) &&
                ! preg_match('/'.implode('|', $this->_excludeDirs).'/', $fullPath)) {

                $this->optimizeImage($fullPath, $input, $output);
            }
        }
    }
    
    /**
     * optimizeImage
     * @param  String          $image
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function optimizeImage($image, InputInterface $input, OutputInterface $output)
    {
        try {
            $source = \Tinify\fromFile($image);
            $source->toFile($image);
        } catch(Exception $e) {
            $output->writeln("Error: {$e}");
        }
        $output->writeln("Optimized by tinypng {$image}.");
    }
}

