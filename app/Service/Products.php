<?php
/**
 * Products.php
 *
 * @copyright 2021/1/15 12:22
 * @author bailu <zhanghang@linghit.com>
 */

namespace Service\Product;

use LinghitExts\Util\Pager\Pager;
use Model\Product\ProductTags;
use Model\Tag\Tags;
use Service\AbstractService;
use Model\Product\Products as MProducts;

class Products extends AbstractService
{
    public function getProductList()
    {
        $builder = MProducts::query()->with('tags');

        if ($this->tag_arr) {
            $tag_arr = json_decode($this->tag_arr, true);
            $tag_arr = $tag_arr ?: ['-1'];
        } elseif ($this->tag) {
            $tag_arr = [$this->tag];
        }

        if (!empty($tag_arr)) {
            $productIds = $this->getProductIds($tag_arr);
            // 单独做分页
            $page = $this->page ?: 1;
            $perPage = $this->per_page ?: 15;
            $count = count($productIds);
            $productIds = array_slice($productIds, ($page - 1) * $perPage, $perPage);
            $list = $count ? $builder->whereIn('id', $productIds)->get()->toArray() : [];
            return [
                'total' => intval($count),
                'current' => intval($page),
                'per_page' => intval($perPage),
                'total_page' => ceil($count / $perPage),
                'from' => 0,
                'to' => 0,
                'list' => $list
            ];
        }

        return Pager::setBuilder($builder->orderByDesc('created_at'))->toArray();
    }

    protected function getProductIds(array $tagArr)
    {
        // 查标签下的所有子标签，以及子标签的子标签（递归）
        $tagArr = $this->getTagsChild($tagArr);

        $builder = [];

        foreach ($tagArr as $rootTagId => $item) {
            if (!$item) {
                continue;
            }
            array_walk($item, function (&$value) {
                $value = "'{$value}'";
            });
            $item = implode(',', $item);
            $builder[] = <<<SQL
SELECT product_id, root_id FROM product_tags LEFT JOIN tags ON tags.id = tag_id
WHERE tag_id IN ($item)
AND product_tags.deleted_at IS NULL
SQL;
        }

        if (!$builder) {
            return [];
        }

        $sql = <<<SQL
SELECT product_id
FROM
(
SELECT product_id
FROM
(

SQL;
        foreach ($builder as $key => $item) {
            if ($key < count($builder) - 1) {
                $sql .= <<<SQL
$item
UNION ALL

SQL;
            } else {
                $sql .= <<<SQL
$item
SQL;
            }
        }
        $count = count($builder) - 1;
        $sql .= <<<SQL

) t
GROUP BY product_id, root_id
) t1
GROUP BY product_id
HAVING count(product_id) > $count ORDER BY product_id ASC
SQL;

        $ret = ProductTags::getConnectionResolver()->connection()->select($sql);

        if ($ret) {
            $ret = json_encode($ret);
            return json_decode($ret, true);
        }

        return [];
    }

    protected function getTagsChild($tagArr)
    {
        $ret = [];

        // 同树为并集，异树为交集
        foreach ($tagArr as $tag) {
            $tag = Tags::query()->find($tag, ['id', 'root_id', 'hierarchy']);
            if (!$tag) {
                continue;
            }
            $tmpTagIds = [$tag->id];
            $rootId = $tag->hierarchy == 1 ? $tag->id : $tag->root_id;
            $childTags = Tags::query()
                ->where('root_id', $rootId)
                ->where('hierarchy', '>', $tag->hierarchy)
                ->orderBy('hierarchy')
                ->get(['id', 'parent_id']);
            foreach ($childTags as $child) {
                if (!in_array($child->parent_id, $tmpTagIds)) {
                    continue;
                }
                $tmpTagIds[] = $child->id;
            }
            $ret[$rootId] = array_merge($ret[$rootId] ?? [], $tmpTagIds);
            $ret[$rootId] = array_unique($ret[$rootId]);
        }

        return $ret;
    }

}