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
 * Base logic common to all filter systems
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
interface FilterInterface
{
    public function getQueryHandlerConfiguration(): QueryHandlerConfigurationInterface;

    public function getCode(): string;

    public function getAttributes(): array;

    public function getFilterType(): string;

    /**
     * @return string|null
     */
    public function getLabel();

    public function getOptions(): array;

    public function getDefault();

    public function setDefault($value);

    /**
     * Override form type from default filter type
     *
     * @return string|null
     */
    public function getFormType();

    public function getFormOptions(): array;

    public function getOption(string $key, $default = null);
}
