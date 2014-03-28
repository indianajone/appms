<?php namespace Libs\Extensions;

use Illuminate\Pagination\Paginator as BasePaginator;

class Paginator extends BasePaginator
{
	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'total' => $this->total, 
			'limit' => $this->perPage,
			// 'current_page' => $this->currentPage, 
			// 'last_page' => $this->lastPage,
			'offset' => $this->from, 
			// 'to' => $this->to, 
			'entries' => $this->getCollection()->toArray(),
		);
	}
}