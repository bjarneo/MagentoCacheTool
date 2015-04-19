<?php
/**
 * Clear cache CLI style
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
     * Get product image model
     *
     */
    private function _getProductImageModel()
    {
        return Mage::getModel('catalog/product_image');
    }

    /**
     * Dispatch event
     *
     */
    private function _dispatchEvent($event)
    {
        Mage::dispatchEvent($event);
    }

    /**
     * Clear cache
     *
     */
    private function _clearCache()
    {
        try {
            $this->_getApp()->getCache()->clean();

            $this->_getApp()->getCacheInstance()->flush();

            $this->_dispatchEvent('adminhtml_cache_flush_all');

            echo 'Cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
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

            $this->_dispatchEvent('clean_catalog_images_cache_after');

            echo 'Image cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
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

            $this->_dispatchEvent('clean_media_cache_after');

            echo 'JavaScript and CSS cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Clear swatches images cache
     *
     */
    private function _clearSwatchesCache()
    {
        try {
            Mage::helper('configurableswatches/productimg')->clearSwatchesCache();

            $this->_dispatchEvent('clean_configurable_swatches_cache_after');

            echo 'Swatches cache cleared ' . PHP_EOL;
        }  catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Clear system cache
     *
     */
    private function _clearSystemCache()
    {
        try {
            $this->_getApp()->cleanCache();

            $this->_dispatchEvent('adminhtml_cache_flush_system');

            echo 'System cache cleared ' . PHP_EOL;
        }  catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }
    }


    private function _clearAll()
    {
        $this->_clearCache();

        $this->_clearImageCache();

        $this->_clearAssetsCache();

        $this->_clearSwatchesCache();

        $this->_clearSystemCache();
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
        } else if ($this->getArg('clear-swatches-cache')) {
            $this->_clearSwatchesCache();
        } else if ($this->getArg('clear-system-cache')) {
            $this->_clearSystemCache();
        } else if ($this->getArg('clear-all')) {
            $this->_clearAll();
        } else {
            echo $this->_usageHelp();
        }
    }


    /**
     * Retrieve Usage Help Message
     *
     */
    private function _usageHelp()
    {
        return <<<USAGE
Usage:  php cache.php -- [options]

  --clear-cache                 Clear cache
  --clear-image-cache           Clear image cache
  --clear-assets                Clear JavaScript and CSS cache
  --clear-swatches-cache        Clear swatches cache
  --clear-system-cache          Clear system cache
  --clear-all                   Clear all cache

  --help                        This help

USAGE;
    }
}

$shell = new Mage_Shell_Cache();
$shell->run();
