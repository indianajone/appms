<?php namespace Kitti\Galleries;

interface GalleryRepositoryInterface extends \AbstractRepositoryInterface
{
	public function owner($name);
}