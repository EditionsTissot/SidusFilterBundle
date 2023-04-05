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
 * Use in combination with a custom form type to provide choices
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class CustomChoiceFilterType extends AbstractSimpleFilterType
{
    /**
     * {@inheritDoc}
     */
    protected function applyDQL(QueryBuilder $qb, string $column, $data): string
    {
        $uid = uniqid('choices', false);
        $qb->setParameter($uid, $data);

        if (is_iterable($data) && count($data) > 1) {
            return "{$column} IN (:{$uid})";
        }

        if (is_array($data) && 1 == count($data) && null === $data[0]) {
            $currentParameters = $qb->getParameters()->filter(function ($parameter) use ($uid) {
                return $parameter->getName() === $uid;
            })->first();

            $qb->getParameters()->removeElement($currentParameters);
            return "{$column} IS NULL";
        }

        return "{$column} = :{$uid}";
    }
}
