<?php
/*
 * This file is part of the Sidus/FilterBundle package.
 *
 * Copyright (c) 2015-2021 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sidus\FilterBundle\Filter;

use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;

/**
 * Default filter implementation, you should not need to customize this class
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
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

    protected $default;

    /**
     * @param string $formType
     * @param string $label
     * @param null   $default
     */
    public function __construct(
        QueryHandlerConfigurationInterface $queryHandlerConfiguration,
        string $code,
        string $filterType,
        array $attributes = [],
        string $formType = null,
        string $label = null,
        array $options = [],
        array $formOptions = [],
        $default = null
    ) {
        $this->queryHandlerConfiguration = $queryHandlerConfiguration;
        $this->code = $code;
        $this->filterType = $filterType;
        $this->attributes = $attributes;
        $this->formType = $formType;
        $this->label = $label;
        $this->options = $options;
        $this->formOptions = $formOptions;
        $this->default = $default;

        if (empty($attributes)) {
            $this->attributes = [$code];
        }
    }

    public function getQueryHandlerConfiguration(): QueryHandlerConfigurationInterface
    {
        return $this->queryHandlerConfiguration;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getFilterType(): string
    {
        return $this->filterType;
    }

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

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function getOption(string $key, $default = null)
    {
        if (!array_key_exists($key, $this->options)) {
            return $default;
        }

        return $this->options[$key];
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }
}
