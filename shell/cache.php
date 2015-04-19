<?php
/**
 * Clear cache cli style
 *
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2015 Bjarne Oeverli (http://bjarneo.codes)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Cache Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Bjarne Oeverli <bjarne.oeverli@gmail.com>
 */
class Mage_Shell_Cache extends Mage_Shell_Abstract
{

    /**
     * Get Mage app
     *
     */
    private function _getApp()
    {
        return Mage::app();
    }

    /**
     * Get Mage app
     *
     */
    private function _getProductImageModel()
    {
        return Mage::getModel('catalog/product_image');
    }


    /**
     * Clear cache
     *
     */
    private function _clearCache()
    {
        try {
            $this->_getApp()->getCache()->clean();

            printf("Cache cleared\r\n");
        } catch (Mage_Core_Exception $e) {
            printf($e->getMessage());
        }
    }

    /**
     * Clear image cache
     *
     */
    private function _clearImageCache()
    {
        try {
            $this->_getProductImageModel()->clearCache();

            printf("Image cache cleared\r\n");
        } catch (Mage_Core_Exception $e) {
            printf($e->getMessage());
        }
    }

    /**
     * Clear assets cache
     *
     */
    private function _clearAssetsCache()
    {
        try {
            Mage::getDesign()->cleanMergedJsCss();

            printf("JavaScript and CSS cache cleared\r\n");
        } catch (Mage_Core_Exception $e) {
            printf($e->getMessage());
        }
    }

    private function _clearAll()
    {
        $this->_clearCache();

        $this->_clearImageCache();

        $this->_clearAssetsCache();
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('clear-cache')) {
            $this->_clearCache();
        } else if ($this->getArg('clear-image-cache')) {
            $this->_clearImageCache();
        } else if ($this->getArg('clear-assets')) {
            $this->_clearAssetsCache();
        } else if ($this->getArg('clear-all')) {
            $this->_clearAll();
        } else {
            echo $this->usageHelp();
        }
    }


    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php cache.php -- [options]

  --clear-cache                 Clear cache
  --clear-image-cache           Clear image cache
  --clear-assets                Clear JavaScript and CSS cache
  --clear-all                   Clear all cache

  --help                        This help

USAGE;
    }
}

$shell = new Mage_Shell_Cache();
$shell->run();
