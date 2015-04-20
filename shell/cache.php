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
 * @category    BjarneoCodes
 * @package     Mage_Shell
 * @author      Bjarne Oeverli <bjarne.oeverli@gmail.com>
 */
class BjarneoCodes_Shell_Cache extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('clear-storage')) {
            $this->_clearStorageCache();
        } else if ($this->getArg('clear-image')) {
            $this->_clearImageCache();
        } else if ($this->getArg('clear-assets')) {
            $this->_clearAssetsCache();
        } else if ($this->getArg('clear-swatches')) {
            $this->_clearSwatchesCache();
        } else if ($this->getArg('clear-system')) {
            $this->_clearSystemCache();
        } else if ($this->getArg('clear-all')) {
            $this->_clearAll();
        } else {
            echo $this->usageHelp();
        }
    }

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
    private function _clearStorageCache()
    {
        try {
            $this->_getApp()->getCacheInstance()->flush();

            $this->_dispatchEvent('shell_cache_flush_all_after');

            echo 'Cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            throw $e;
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

            $this->_dispatchEvent('shell_clean_catalog_images_cache_after');

            echo 'Image cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            throw $e;
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

            $this->_dispatchEvent('shell_clean_media_cache_after');

            echo 'JavaScript and CSS cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            throw $e;
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

            $this->_dispatchEvent('shell_configurable_swatches_cache_after');

            echo 'Swatches cache cleared ' . PHP_EOL;
        }  catch (Mage_Core_Exception $e) {
            throw $e;
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

            $this->_dispatchEvent('shell_cache_flush_system_after');

            echo 'System cache cleared ' . PHP_EOL;
        }  catch (Mage_Core_Exception $e) {
            throw $e;
        }
    }


    private function _clearAll()
    {
        $this->_clearStorageCache();

        $this->_clearImageCache();

        $this->_clearAssetsCache();

        $this->_clearSwatchesCache();

        $this->_clearSystemCache();

        $this->_dispatchEvent('shell_clear_all_cache_after');
    }


    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php cache.php -- [options]

  --clear-storage               Clear storage cache
  --clear-image                 Clear image cache
  --clear-assets                Clear JavaScript and CSS cache
  --clear-swatches              Clear swatches cache
  --clear-system                Clear system cache
  --clear-all                   Clear all cache

  --help                        This help

USAGE;
    }
}

$shell = new BjarneoCodes_Shell_Cache();
$shell->run();
