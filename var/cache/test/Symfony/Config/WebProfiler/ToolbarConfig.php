<?php

namespace Symfony\Config\WebProfiler;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ToolbarConfig 
{
    private $enabled;
    private $ajaxReplace;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * Replace toolbar on AJAX requests
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function ajaxReplace($value): static
    {
        $this->_usedProperties['ajaxReplace'] = true;
        $this->ajaxReplace = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('ajax_replace', $value)) {
            $this->_usedProperties['ajaxReplace'] = true;
            $this->ajaxReplace = $value['ajax_replace'];
            unset($value['ajax_replace']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['ajaxReplace'])) {
            $output['ajax_replace'] = $this->ajaxReplace;
        }

        return $output;
    }

}
