<?php
namespace Insight\Contracts;

interface Repository {

	public function get();

	public function getById($id);

	public function getAll();
}
