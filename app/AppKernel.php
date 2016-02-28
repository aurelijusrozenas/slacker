<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/* Debug functions */
if (!function_exists('p')) {
    function p($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, false, true, 'var_dump');
    }
}
if (!function_exists('pe')) {
    function pe($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, true, true, 'var_dump');
    }
}
if (!function_exists('j')) {
    function j($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, false, true, 'json_encode');
    }
}
if (!function_exists('je')) {
    function je($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, true, true, 'json_encode');
    }
}
if (!function_exists('d')) {
    function d($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, false, true, 'doctrine_dump');
    }
}
if (!function_exists('de')) {
    function de($obj = '', $title = '')
    {
        printDebugInfo($obj, $title, true, true, 'doctrine_dump');
    }
}
if (!function_exists('printDebugInfo')) {
    function printDebugInfo($obj, $title, $exit, $printPreTag, $format = '')
    {
        if ($printPreTag) {
            echo '<pre>';
        }
        if ($title) {
            echo $title.":\n<br />";
        }
        switch ($format) {
            case 'json_encode':
                echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                break;
            case 'doctrine_dump':
                \Doctrine\Common\Util\Debug::dump($obj);
                break;
            default:
                var_dump($obj);
        }
        if ($printPreTag) {
            echo '</pre>';
        }
        if ($exit) {
            exit();
        }
    }
}

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
