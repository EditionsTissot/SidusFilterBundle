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

namespace Sidus\FilterBundle\Filter\Type\Doctrine;

use Doctrine\ORM\QueryBuilder;

/**
 * Simple test to check if column has values
 */
class NotNullFilterType extends AbstractSimpleFilterType
{
    /**
     * Must return the DQL statement and set the proper parameters in the QueryBuilder
     */
    protected function applyDQL(QueryBuilder $qb, string $column, $data): string
    {
        return "{$column} IS NOT NULL";
    }

    protected function isEmpty($data): bool
    {
        return empty($data);
    }
}
