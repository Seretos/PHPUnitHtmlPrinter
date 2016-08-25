<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 23.08.2016
 * Time: 20:38
 */

namespace PHPUnitHtmlPrinter\listener;


use PHPUnitHtmlPrinter\manager\SeleniumResultTreeManager;
use Twig_Loader_Filesystem;

class TestResultListenerFactory
{
    public function createManager()
    {
        return new SeleniumResultTreeManager();
    }

    public function createTwig()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../Resources/views');
        $twig = new \Twig_Environment($loader, array());
        return $twig;
    }

    /**
     * @param $filename
     * @param $content
     * @codeCoverageIgnore
     */
    public function saveContent($filename, $content)
    {
        file_put_contents($filename, $content);
    }
}