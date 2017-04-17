<?php

namespace Gabrielqs\ThemeSettings\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\View\Page\Config as PageConfig;
use \Magento\Framework\View\Layout;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Request\Http;

class ApplyLayoutUpdates implements ObserverInterface
{
    # Header constants
    const HEADER_LAYOUT_CONFIG_PATH = 'design/theme_settings/header_layout';
    const HEADER_LAYOUT_HANDLE_PREFIX = 'gabrielqs_themesettings_header_';

    # Footer constants
    const FOOTER_LAYOUT_CONFIG_PATH = 'design/theme_settings/footer_layout';
    const FOOTER_LAYOUT_HANDLE_PREFIX = 'gabrielqs_themesettings_footer_';

    # Product Grid Constants
    const PRODUCTLISTITEM_LAYOUT_CONFIG_PATH = 'design/theme_settings/product_list_item_layout';
    const PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX = 'gabrielqs_themesettings_product_list_item_';
    const PRODUCTSEARCHITEM_LAYOUT_HANDLE_PREFIX = 'gabrielqs_themesettings_product_search_item_';
    const PRODUCTLISTITEM_HANDLES = [
        'cms_index_index',
        'catalog_category_view',
    ];
    const PRODUCTSEARCHITEM_HANDLES = [
        'catalogsearch_result_index',
        'catalogsearch_advanced_result'
    ];

    # Theme Customization Constants
    const THEME_CUSOMIZATION_HANDLE_NAME = 'gabrielqs_themesettings_customization';

    /**
     * Full Action Name
     * @var string
     */
    protected $_fullActionName;

    /**
     * Request - Temporary until we have a banners module
     * @var Http
     */
    protected $_request;

    /**
     * Scope Config
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * ApplyHeader constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,

        # Temporary until we have a banners module
        Http $request

    ) {
        $this->_scopeConfig = $scopeConfig;

        # Temporary until we have a banners module
        $this->_request = $request;
    }

    /**
     * Applies update handles based on config settings
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Layout $layout */
        if (!$layout = $observer->getData('layout')) {
            return;
        }

        if (!$update = $layout->getUpdate()) {
            return;
        }

        # Set footer - Applied to all pages
        $update->addHandle($this->_getFooterHandleName());

        # Set header - Applied to all pages
        $update->addHandle($this->_getHeaderHandleName());

        # Set footer - Applied product list pages only
        if ($this->_shouldApplyProductListItemHandle()) {
            $update->addHandle($this->_getProductListItemHandleName());
        }

        # Temporary until we have a banners module
        # Adds the banner handle only when at the home page
        if ($this->_shouldApplyBannerHandle()) {
            $update->addHandle($this->_getBannerHandleName());
        }

        # In all pages, add the theme customization AFTER all of the above
        $update->addHandle(self::THEME_CUSOMIZATION_HANDLE_NAME);
    }

    /**
     * Returns footer update handle name based on config setting
     * @return string
     */
    protected function _getFooterHandleName()
    {
        $handleCode = $this->_scopeConfig->getValue(
            self::FOOTER_LAYOUT_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );
        if (!$handleCode) {
            $handleCode = 1;
        }
        return self::FOOTER_LAYOUT_HANDLE_PREFIX . $handleCode;
    }

    /**
     * Returns header update handle name based on config setting
     * @return string
     */
    protected function _getHeaderHandleName()
    {
        $handleCode = $this->_scopeConfig->getValue(
            self::HEADER_LAYOUT_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );
        if (!$handleCode) {
            $handleCode = 1;
        }
        return self::HEADER_LAYOUT_HANDLE_PREFIX . $handleCode;
    }

    /**
     * Returns productListItem update handle name based on config setting
     * @return string
     */
    protected function _getProductListItemHandleName()
    {
        $handleCode = $this->_scopeConfig->getValue(
            self::PRODUCTLISTITEM_LAYOUT_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );
        if (!$handleCode) {
            $handleCode = 1;
        }

        if ($this->_isSearch()) {
            $return = self::PRODUCTSEARCHITEM_LAYOUT_HANDLE_PREFIX . $handleCode;
        } else {
            $return = self::PRODUCTLISTITEM_LAYOUT_HANDLE_PREFIX . $handleCode;
        }

        return $return;
    }

    /**
     * Returns current action full name
     * @return string
     */
    protected function _getFullActionName()
    {
        if ($this->_fullActionName == null) {
            $this->_fullActionName = $this->_request->getFullActionName();
        }
        return $this->_fullActionName;
    }

    /**
     * Handles which will receive the product listitem update handle
     * @return string[]
     */
    protected function _getProductListItemHandles()
    {
        return array_merge(self::PRODUCTLISTITEM_HANDLES, self::PRODUCTSEARCHITEM_HANDLES);
    }

    /**
     * Are we in a search page?
     * @return bool
     */
    protected function _isSearch()
    {

        return in_array($this->_getFullActionName(), self::PRODUCTSEARCHITEM_HANDLES);
    }

    /**
     * Decides wether we should apply the product listitem handle
     * @return bool
     */
    protected function _shouldApplyProductListItemHandle()
    {
        return in_array($this->_getFullActionName(), $this->_getProductListItemHandles());
    }


    # Temporary until we have a banners module
    const BANNER_LAYOUT_CONFIG_PATH = 'design/theme_settings/home_banners_layout';
    const BANNER_LAYOUT_HANDLE_PREFIX = 'gabrielqs_themesettings_banner_';
    const BANNER_AFFECTED_HANDLES = [
        'cms_index_index'
    ];

    /**
     * Returns banner template name name based on config setting - Temporary until we have a banners module
     * @return string
     */
    protected function _getBannerHandleName()
    {
        $handleCode = $this->_scopeConfig->getValue(
            self::BANNER_LAYOUT_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );
        if (!$handleCode) {
            $handleCode = 1;
        }
        return self::BANNER_LAYOUT_HANDLE_PREFIX . $handleCode;
    }

    /**
     * Handles which will receive the banner update handle - Temporary until we have a banner module
     * @return string[]
     */
    protected function _getBannerHandles()
    {
        return self::BANNER_AFFECTED_HANDLES;
    }

    /**
     * Decides wether we should apply the banner handle- Temporary until we have a banner module
     * @return bool
     */
    protected function _shouldApplyBannerHandle()
    {
        return in_array($this->_getFullActionName(), $this->_getBannerHandles());
    }
}
