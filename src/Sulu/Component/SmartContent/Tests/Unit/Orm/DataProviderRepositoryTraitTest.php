<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\SmartContent\Tests\Orm;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class Query extends AbstractQuery
{
    public function setFirstResult()
    {
    }

    public function setMaxResults()
    {
    }

    public function getSQL()
    {
    }

    public function _doExecute()
    {
    }
}

class DataProviderRepositoryTraitTest extends TestCase
{
    /**
     * @var DataProviderRepositoryTrait
     */
    private $dataProviderRepositoryTrait;

    public function setUp(): void
    {
        $this->dataProviderRepositoryTrait = $this->getMockForTrait(DataProviderRepositoryTrait::class);
    }

    public function testFindByFiltersIds()
    {
        $findByFiltersIdsReflection = new \ReflectionMethod(
            get_class($this->dataProviderRepositoryTrait),
            'findByFiltersIds'
        );
        $findByFiltersIdsReflection->setAccessible(true);

        $query = $this->prophesize(Query::class);
        $query->setFirstResult(0)->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->getScalarResult()->willReturn([]);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->distinct(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->dataProviderRepositoryTrait->method('createQueryBuilder')->willReturn($queryBuilder->reveal());

        $findByFiltersIdsReflection->invoke($this->dataProviderRepositoryTrait, [], 1, 5, null, 'de');

        // using distinct here is essential, since due to our joins multiple rows might be returned
        // this makes problems if also a limit is used
        $queryBuilder->distinct()->shouldBeCalled();
    }

    public function testFindByFiltersIdsWithDatasourceWithoutIncludeSubFolders()
    {
        $findByFiltersIdsReflection = new \ReflectionMethod(
            get_class($this->dataProviderRepositoryTrait),
            'findByFiltersIds'
        );
        $findByFiltersIdsReflection->setAccessible(true);

        $query = $this->prophesize(Query::class);
        $query->setFirstResult(0)->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->getScalarResult()->willReturn([]);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->distinct(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->dataProviderRepositoryTrait->method('createQueryBuilder')->willReturn($queryBuilder->reveal());

        $findByFiltersIdsReflection->invoke($this->dataProviderRepositoryTrait, ['dataSource' => 3], 1, 5, null, 'de');

        // using distinct here is essential, since due to our joins multiple rows might be returned
        // this makes problems if also a limit is used
        $queryBuilder->distinct()->shouldBeCalled();
    }

    public function testFindByFiltersWithSorting()
    {
        $query = $this->prophesize(Query::class);

        $query = $this->prophesize(Query::class);
        $query->setParameter(Argument::cetera())->willReturn($query);
        $query->setFirstResult(0)->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->getScalarResult()->willReturn([]);
        $query->getResult()->willReturn([]);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->addSelect(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->distinct(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->getAllAliases()->willReturn([]);
        $queryBuilder->getQuery()->willReturn($query);
        $this->dataProviderRepositoryTrait->method('createQueryBuilder')->willReturn($queryBuilder->reveal());
        $this->dataProviderRepositoryTrait->findByFilters(
            ['sortBy' => 'test', 'sortMethod' => 'asc'],
            1,
            5,
            null,
            'de'
        );

        $queryBuilder->orderBy('test', 'asc')->shouldBeCalled();
    }

    public function testFindByFiltersWithoutSorting()
    {
        $query = $this->prophesize(Query::class);

        $query = $this->prophesize(Query::class);
        $query->setParameter(Argument::cetera())->willReturn($query);
        $query->setFirstResult(0)->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->getScalarResult()->willReturn([]);
        $query->getResult()->willReturn([]);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->addSelect(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->distinct(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->getAllAliases()->willReturn([]);
        $queryBuilder->getQuery()->willReturn($query);
        $this->dataProviderRepositoryTrait->method('createQueryBuilder')->willReturn($queryBuilder->reveal());
        $this->dataProviderRepositoryTrait->findByFilters(
            ['sortBy' => null, 'sortMethod' => 'asc'],
            1,
            5,
            null,
            'de'
        );

        $queryBuilder->orderBy(null, 'asc')->shouldNotBeCalled();
    }
}
