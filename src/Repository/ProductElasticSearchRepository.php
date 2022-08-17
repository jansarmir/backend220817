<?php

namespace App\Repository;

class ProductElasticSearchRepository
{
    private $client;

    public function __construct()
    {
//        $this->client = $client;
    }

    /**
     * @param array $result
     * @return int[]
     */
    protected function extractIds(array $result): array
    {
        $hits = $result['hits']['hits'];
        return array_column($hits, '_id');
    }

    /**
     * @param array $result
     * @return int
     */
    protected function extractTotalCount(array $result): int
    {
        return (int)$result['hits']['total']['value'];
    }

    protected function createQuery(string $indexName, string $searchText): array
    {
        $searchText = $searchText ?? '';

        //$query = ...;

        return $query;
    }

    /**
     * @param string|null $searchText
     * @return int[]
     */
    public function getIdsBySearchText(?string $searchText): array
    {
        if ($searchText === null || $searchText === '') {
            return [];
        }

        //$queryBuilder-> ... $searchText

        return $this->getIdsByQueryBuilder($queryBuilder);
    }

    /**
     * @param int $page
     * @return int[]
     */
    public function getIdsByPage(int $page): array
    {
        //$queryBuilder-> ... $page

        return $this->getIdsByQueryBuilder($queryBuilder);
    }

    /**
     * @param $queryBuilder
     * @return int[]
     */
    private function getIdsByQueryBuilder($queryBuilder): array
    {
        //set LIMIT
        $result = $this->client->search($queryBuilder);

        return $this->extractIds($result);
    }

    public function exportProductArray(array $array): void
    {
        //push data to elastic
    }
}
