<?php

declare(strict_types = 1);

namespace Model\Repository;

use Model\Entity;

class Product
{
    private $identityMap;
    public function __construct(IdentityMap $identityMap)
    {
        /**
         * $this->identityMap = код загрузки состояния $identityMap из глобального state'a или из session (по принципу как реализован Basket в это проекте)
         */

    }

    /**
     * Поиск продуктов по массиву id
     *
     * @param int[] $ids
     * @return Entity\Product[]
     */
    public function search(array $ids = []): array
    {
        if (!count($ids)) {
            return [];
        }

        $productList = [];

//id, которые на найдены в identity map
        $idsNotInCache = [];

        foreach ($ids as $id){
            $product=$this->identityMap->get('Product',$id);

// если продукт не найден в identity map, то его id запоминаем и потом сделаем запрос в БД
            if(!$product){
                $idsNotInCache[]=$id;
            } else {
                $productList[]=$product;
            }
        }

// недостающие id загружаем из БД и помещаем их Identity Map
        foreach ($this->getDataFromSource(['id' => $idsNotInCache]) as $item) {
            $product = new Entity\Product($item['id'], $item['name'], $item['price']);
            $productList[]=$product;
            $this->identityMap->add($product);
        }

        return $productList;
    }

    /**
     * Получаем все продукты
     *
     * @return Entity\Product[]
     */
    public function fetchAll(): array
    {
        $productList = [];
// при загрузке всех данных происходит только обновление identity map (который потом можно использованть в запросе по ids)
// здесь у нас нет критерия (набор уникальных полей) по которым мы могли бы принять решение загружать данные из identity, а не из БД
        foreach ($this->getDataFromSource() as $item) {
            $product = new Entity\Product($item['id'], $item['name'], $item['price']);
            $productList[]=$product;
            $this->identityMap->add($product);
        }

        return $productList;
    }

    /**
     * Получаем продукты из источника данных
     *
     * @param array $search
     *
     * @return array
     */
    private function getDataFromSource(array $search = [])
    {
        $dataSource = [
            [
                'id' => 1,
                'name' => 'PHP',
                'price' => 15300,
            ],
            [
                'id' => 2,
                'name' => 'Python',
                'price' => 20400,
            ],
            [
                'id' => 3,
                'name' => 'C#',
                'price' => 30100,
            ],
            [
                'id' => 4,
                'name' => 'Java',
                'price' => 30600,
            ],
            [
                'id' => 5,
                'name' => 'Ruby',
                'price' => 18600,
            ],
            [
                'id' => 8,
                'name' => 'Delphi',
                'price' => 8400,
            ],
            [
                'id' => 9,
                'name' => 'C++',
                'price' => 19300,
            ],
            [
                'id' => 10,
                'name' => 'C',
                'price' => 12800,
            ],
            [
                'id' => 11,
                'name' => 'Lua',
                'price' => 5000,
            ],
        ];

        if (!count($search)) {
            return $dataSource;
        }

        $productFilter = function (array $dataSource) use ($search): bool {
            return in_array($dataSource[key($search)], current($search), true);
        };

        return array_filter($dataSource, $productFilter);
    }
}
