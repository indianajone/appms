<?php

class ArticlesTest extends TestCase {
	public function testArticlesField() {
		$response = $this->call('GET', 'api/v1/articles/fields');
		$this->assertResponseStatus(200);
	}

	public function testArticlesGetAllData() {
		$response = $this->call('GET', 'api/v1/articles');
		$this->assertResponseStatus(200);
	}

	public function testArticlesGetByID() {
		$response = $this->call('GET', 'api/v1/articles/1');
		$this->assertResponseStatus(200);
	}

	public function testArticleFind() {
		$data = array(

		);
		
		$response = $this->call('GET', 'api/v1/articles/find', $data);
		$this->assertResponseStatus(200);
	}
}