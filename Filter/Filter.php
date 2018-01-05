<?php

namespace Sidus\FilterBundle\Filter;

use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;

/**
 * Base filter logic
 */
class Filter implements FilterInterface
{
    /** @var QueryHandlerConfigurationInterface */
    protected $queryHandlerConfiguration;

    /** @var string */
    protected $code;

    /** @var string */
    protected $filterType;

    /** @var array */
    protected $attributes = [];

    /** @var string */
    protected $formType;

    /** @var string */
    protected $label;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $formOptions = [];

    /**
     * @param QueryHandlerConfigurationInterface $queryHandlerConfiguration
     * @param string                             $code
     * @param string                             $filterType
     * @param array                              $attributes
     * @param string                             $formType
     * @param string                             $label
     * @param array                              $options
     * @param array                              $formOptions
     */
    public function __construct(
        QueryHandlerConfigurationInterface $queryHandlerConfiguration,
        string $code,
        string $filterType,
        array $attributes = [],
        string $formType = null,
        string $label = null,
        array $options = [],
        array $formOptions = []
    ) {
        $this->queryHandlerConfiguration = $queryHandlerConfiguration;
        $this->code = $code;
        $this->filterType = $filterType;
        $this->attributes = $attributes;
        $this->formType = $formType;
        $this->label = $label;
        $this->options = $options;
        $this->formOptions = $formOptions;

        if (empty($attributes)) {
            $this->attributes = [$code];
        }
    }

    /**
     * @return QueryHandlerConfigurationInterface
     */
    public function getQueryHandlerConfiguration(): QueryHandlerConfigurationInterface
    {
        return $this->queryHandlerConfiguration;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getFilterType(): string
    {
        return $this->filterType;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return array
     */
    public function getFormOptions(): array
    {
        return $this->formOptions;
    }
}