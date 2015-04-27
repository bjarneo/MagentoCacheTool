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
        } else if ($this->getArg('enable-type')) {
            $this->_toggleType($this->getArg('enable-type'));
        } else if ($this->getArg('disable-type')) {
            $this->_toggleType($this->getArg('disable-type'), 0);
        } else if ($this->getArg('enable-types')) {
            $this->_toggleAllTypes(1);
        } else if ($this->getArg('disable-types')) {
            $this->_toggleAllTypes(0);
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
    private function _dispatchEvent($event, $data = [])
    {
        Mage::dispatchEvent($event, $data);
    }

    /**
     * Clear cache
     *
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    private function _clearSystemCache()
    {
        try {
            $this->_getApp()->cleanCache();

            $this->_dispatchEvent('shell_cache_flush_system_after');

            echo 'System cache cleared ' . PHP_EOL;
        } catch (Mage_Core_Exception $e) {
            throw $e;
        }
    }

    /**
     * Toggle cache type
     *
     * @param string $type cache type
     * @param int $state the current state you want to put it in
     * @throws Mage_Core_Exception
     * @return bool
     */
    private function _toggleType($type = '', $state = 1)
    {
        $types = $this->_getApp()->useCache();

        $type = strtolower($type);

        if (isset($types[$type])) {
            $types[$type] = $state;

            try {
                $this->_getApp()->saveUseCache($types);

                $this->_dispatchEvent(
                    sprintf('shell_toggle_cache_type_%s', $type),
                    [ 'state' => (bool) $state ]
                );

                printf('Cache type %s was toggled %s', $type, $state ? 'on' : 'off');

                return true;
            } catch(Mage_Core_Exception $e) {
                throw $e;
            }
        }

        return false;
    }

    /**
     * Toggle all types
     *
     * @param int $state on / off
     */
    private function _toggleAllTypes($state = 1)
    {
        $types = array_fill_keys(
            array_keys($this->_getApp()->useCache()),
            $state
        );

        try {
            $this->_getApp()->saveUseCache($types);

            $this->_dispatchEvent(
                'shell_toggle_all_cache_types',
                [ 'state' => (bool) $state ]
            );

            printf('All cache types was toggled %s', $state ? 'on' : 'off');
        } catch(Mage_Core_Exception $e) {
            throw $e;
        }
    }

    /**
     * Clear all
     *
     */
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
     * @return string heredoc
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
  --enable-type  <type>         Enable cache type
  --disable-type <type>         Disable cache type
  --enable-types                Enable cache types
  --disable-types               Disable cache types

  --help                        This help

USAGE;
    }
}

$shell = new BjarneoCodes_Shell_Cache();
$shell->run();
