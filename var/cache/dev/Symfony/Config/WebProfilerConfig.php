<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'WebProfiler'.\DIRECTORY_SEPARATOR.'ToolbarConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class WebProfilerConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $toolbar;
    private $interceptRedirects;
    private $excludedAjaxPaths;
    private $_usedProperties = [];

    /**
     * Profiler toolbar configuration
     * @default {"enabled":false,"ajax_replace":false}
    */
    public function toolbar(array $value = []): \Symfony\Config\WebProfiler\ToolbarConfig
    {
        if (null === $this->toolbar) {
            $this->_usedProperties['toolbar'] = true;
            $this->toolbar = new \Symfony\Config\WebProfiler\ToolbarConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "toolbar()" has already been initialized. You cannot pass values the second time you call toolbar().');
        }

        return $this->toolbar;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function interceptRedirects($value): static
    {
        $this->_usedProperties['interceptRedirects'] = true;
        $this->interceptRedirects = $value;

        return $this;
    }

    /**
     * @default '^/((index|app(_[\\w]+)?)\\.php/)?_wdt'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function excludedAjaxPaths($value): static
    {
        $this->_usedProperties['excludedAjaxPaths'] = true;
        $this->excludedAjaxPaths = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'web_profiler';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('toolbar', $value)) {
            $this->_usedProperties['toolbar'] = true;
            $this->toolbar = \is_array($value['toolbar']) ? new \Symfony\Config\WebProfiler\ToolbarConfig($value['toolbar']) : $value['toolbar'];
            unset($value['toolbar']);
        }

        if (array_key_exists('intercept_redirects', $value)) {
            $this->_usedProperties['interceptRedirects'] = true;
            $this->interceptRedirects = $value['intercept_redirects'];
            unset($value['intercept_redirects']);
        }

        if (array_key_exists('excluded_ajax_paths', $value)) {
            $this->_usedProperties['excludedAjaxPaths'] = true;
            $this->excludedAjaxPaths = $value['excluded_ajax_paths'];
            unset($value['excluded_ajax_paths']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['toolbar'])) {
            $output['toolbar'] = $this->toolbar instanceof \Symfony\Config\WebProfiler\ToolbarConfig ? $this->toolbar->toArray() : $this->toolbar;
        }
        if (isset($this->_usedProperties['interceptRedirects'])) {
            $output['intercept_redirects'] = $this->interceptRedirects;
        }
        if (isset($this->_usedProperties['excludedAjaxPaths'])) {
            $output['excluded_ajax_paths'] = $this->excludedAjaxPaths;
        }

        return $output;
    }

}
