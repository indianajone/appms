<?php namespace Indianajone\Categories\Extensions\Eloquent;

use Baum\Extensions\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection {

  public function toHierarchy() {
    $tree = $this->items;

    return new BaseCollection($this->hierarchical($tree));
  }

  protected function hierarchical(&$result) {
    $new = array();

    if ( is_array($result) ) {
      while( list($n, $sub) = each($result) ) {
        $new[] = $sub;

        if ( ! $sub->isLeaf() )
          $sub->setRelation('children', new BaseCollection($this->hierarchical($result)));

        $next_id = key($result);

        if ( $next_id && $result[$next_id]->getParentId() != $sub->getParentId() )
          return $new;
      }
    }

    return $new;
  }

}