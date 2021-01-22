<?php


namespace App\Http\Controllers;


use App\Models\ProductTag;
use App\Models\Tag;

class TestController extends Controller
{
    public function test()
    {
//        $foo = ['161068253000000120', '161068253000000060', '161068253000000090'];
        $foo = ['161068253000000090', '161068253100000070'];


        $tags = Tag::whereIn('id', $foo)->get();

        foreach ($tags as $tag) {
            $tagGroups[$tag['style']][$tag['hierarchy']][$tag['id']] = $tag;
        }

        $tagsResult = [];
        foreach ($tagGroups as $style => $hierarches) {
            ksort($hierarches);

            foreach ($hierarches as $hierarchy => $tagItems) {
                foreach ($tagItems as $tagItem) {
                    if (in_array($tagItem['id'], $tagsResult[$style] ?? [])) {
                        continue;
                    }

                    $tagsResult[$style][] = $this->getChildren($tagItem);
                    $tagsResult[$style] = $this->mergeArray($tagsResult[$style]);
                }
            }
        }

        $productIdGroup = [];
        foreach ($tagsResult as $style => $tagIds) {
            $products = ProductTag::whereIn('tag_id', $tagIds)->get('product_id')->toArray();
            $productIds = array_column($products, 'product_id');

            $productIdGroup = $productIdGroup ? array_intersect($productIdGroup, $productIds) : $productIds;

            if (!$productIdGroup) {
                break;
            }
        }


//        return response($productIdGroup);
         var_dump($tagsResult);
         var_dump($productIdGroup);

    }

    protected function getChildren(Tag $tag)
    {
        $children = $tag->childrenTags()->get()->toArray();

        $children[] = $tag->toArray();

        $flatArray = $this->flattenTree($children);

        return $flatArray;
    }

    protected function flattenTree(array $tree, $column = 'id')
    {
        $flatArray = iterator_to_array(
            new \RecursiveIteratorIterator(
                new \RecursiveCallbackFilterIterator (new \RecursiveArrayIterator($tree),
                    function ($current, $key, $iterator) use ($column){
                        if ($iterator->hasChildren()) {
                            return true;
                        }
                        return $key == $column;
                    })
            ),
            false
        );

        return $flatArray;
    }

    protected function mergeArray($array)
    {
        $merged = iterator_to_array(
            new \RecursiveIteratorIterator(
                new \RecursiveArrayIterator($array)
            ),
            false
        );

        return $merged;
    }
}
