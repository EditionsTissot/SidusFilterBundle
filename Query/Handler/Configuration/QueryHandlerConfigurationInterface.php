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

namespace Sidus\FilterBundle\Query\Handler\Configuration;

use Sidus\FilterBundle\Filter\FilterInterface;

/**
 * Holds the configuration of a query handler
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
interface QueryHandlerConfigurationInterface
{
    public function getCode(): string;

    public function getProvider(): string;

    /**
     * @param int $index
     *
     * @throws \UnexpectedValueException
     */
    public function addFilter(FilterInterface $filter, int $index = null);

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array;

    /**
     * @throws \UnexpectedValueException
     */
    public function getFilter(string $code): FilterInterface;

    public function getSortable(): array;

    public function addSortable(string $sortable);

    /**
     * @return string[]
     */
    public function getDefaultSort(): array;

    public function getResultsPerPage(): int;

    public function getOptions(): array;

    /**
     * @param null $fallback
     */
    public function getOption(string $code, $fallback = null);
}
