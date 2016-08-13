<?php
namespace Insight\Contracts\Repository;

interface IRepository {

	public function get();

	public function getById($id);

	public function getAll();
}
