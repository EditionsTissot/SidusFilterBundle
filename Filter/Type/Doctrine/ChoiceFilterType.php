<?php

namespace Sidus\FilterBundle\Filter\Type\Doctrine;

use Sidus\FilterBundle\Exception\BadQueryHandlerException;
use Sidus\FilterBundle\Filter\FilterInterface;
use Sidus\FilterBundle\Query\Handler\Doctrine\DoctrineQueryHandlerInterface;
use Sidus\FilterBundle\Query\Handler\QueryHandlerInterface;

/**
 * Filter logic for choice with Doctrine entities
 */
class ChoiceFilterType extends AbstractDoctrineFilterType
{
    /**
     * {@inheritdoc}
     */
    public function handleData(QueryHandlerInterface $queryHandler, FilterInterface $filter, $data)
    {
        if (!$queryHandler instanceof DoctrineQueryHandlerInterface) {
            throw new BadQueryHandlerException($queryHandler, DoctrineQueryHandlerInterface::class);
        }
        if (null === $data || (\is_array($data) && 0 === \count($data))) {
            return;
        }
        $dql = [];
        $qb = $queryHandler->getQueryBuilder();
        foreach ($this->getFullAttributeReferences($filter, $queryHandler->getAlias()) as $column) {
            $uid = uniqid('choices');
            if (\is_array($data)) {
                $dql[] = "{$column} IN (:{$uid})";
                $qb->setParameter($uid, $data);
            } else {
                $dql[] = "{$column} = :{$uid}";
                $qb->setParameter($uid, $data);
            }
        }
        if (0 < \count($dql)) {
            $qb->andWhere(implode(' OR ', $dql));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions(QueryHandlerInterface $queryHandler, FilterInterface $filter): array
    {
        if (!$queryHandler instanceof DoctrineQueryHandlerInterface) {
            throw new BadQueryHandlerException($queryHandler, DoctrineQueryHandlerInterface::class);
        }

        if (isset($filter->getFormOptions()['choices'])) {
            return parent::getFormOptions($queryHandler, $filter);
        }

        $choices = [];
        $alias = $queryHandler->getAlias();
        foreach ($this->getFullAttributeReferences($filter, $alias) as $column) {
            $qb = clone $queryHandler->getQueryBuilder();
            $qb->select("{$column} AS __value")
                ->groupBy($column);
            foreach ($qb->getQuery()->getArrayResult() as $result) {
                $value = $result['__value'];
                $choices[$value] = $value;
            }
        }

        return array_merge(
            $this->formOptions,
            $filter->getFormOptions(),
            ['choices' => $choices]
        );
    }
}
