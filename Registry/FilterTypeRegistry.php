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

namespace Sidus\FilterBundle\Registry;

use Sidus\FilterBundle\Filter\Type\FilterTypeInterface;

/**
 * Registry for filter types
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class FilterTypeRegistry
{
    /** @var FilterTypeInterface[][] */
    protected $filterTypes = [];

    public function addFilterType(FilterTypeInterface $filterType): void
    {
        $this->filterTypes[$filterType->getProvider()][$filterType->getName()] = $filterType;
    }

    /**
     * @return FilterTypeInterface[]
     *
     * @throws \UnexpectedValueException
     */
    public function getFilterTypes(string $provider): array
    {
        if (!array_key_exists($provider, $this->filterTypes)) {
            throw new \UnexpectedValueException("No filter types for provider with code : {$provider}");
        }

        return $this->filterTypes[$provider];
    }

    /**
     * @throws \UnexpectedValueException
     */
    public function getFilterType(string $provider, string $code): FilterTypeInterface
    {
        if (!$this->hasFilterType($provider, $code)) {
            $flattenedTypes = implode("', '", array_keys($this->filterTypes[$provider]));
            $m = "No type for provider {$provider} with code : {$code}, ";
            $m .= "available types are '{$flattenedTypes}'.";
            throw new \UnexpectedValueException($m);
        }

        return $this->filterTypes[$provider][$code];
    }

    public function hasFilterType(string $provider, string $code): bool
    {
        return !empty($this->filterTypes[$provider][$code]);
    }
}
