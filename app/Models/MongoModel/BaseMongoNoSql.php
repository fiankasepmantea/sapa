<?php

namespace App\Models\MongoModel;

use Dhiva\Core\MongoLib;
class BaseMongoNoSql
{
	/**
	 * @var Mongo
	 */
	protected $m;
	protected $container = [];

	protected $collection;
	protected $providers = [
		
	];
	/**
	 *
	 */
	public function __construct()
	{
		$this->m = new MongoLib();
	}

	public function __get($name)
	{
		if (!isset($this->providers[$name])) {
			throw new \Exception("class not found");
		} else {
			if (!isset($this->container[$name]) || !$this->container[$name]) {
				try {
					$this->container["{$name}"] = new $this->providers[$name]();
				} catch (\Exception $e) {
					throw new $e;
				}
			}
			return $this->container["{$name}"];
		}
	}
	/**

	 * @return mixed
	 */
	public function getIndexes()
	{
		return $this->m->listindexes($this->collection);
	}

	/**

	 * @param array $credentials
	 * @return mixed
	 */
	public function create( array $credentials)
	{
		return $this->m->insertMany($this->collection, $credentials);
	}

	/**

	 * @param array $credentials
	 * @return mixed
	 */
	public function createOne( array $credentials)
	{
		return $this->m->insertOne($this->collection, $credentials);
	}

	/**

	 * @param array $where
	 * @param array $options
	 * @param array $select
	 * @return mixed
	 * @throws \Exception
	 */
	public function getList( array $where = [], array $options = [], array $select = [])
	{
		$t = $this->m->options($options)->select($select)->where($where)->find($this->collection)->toArray();
		unset($t['_id']);
		return $t;
	}

	/**

	 * @param array $where
	 * @param array $options
	 * @param array $select
	 * @return mixed
	 * @throws \Exception
	 */
	public function getOne( array $where = [], array $options = [], array $select = [])
	{
		$t = $this->m->options($options)->select($select)->where($where)->findOne($this->collection);
		unset($t['_id']);
		return $t;
	}

	/**
	 * @param array $credentials

	 * @return mixed
	 * @throws \Exception
	 */
	public function get_where(array $credentials, )
	{
		$t = $this->m->options(['limit' => 1])->where($credentials)->count($this->collection);
		unset($t['_id']);
		return $t;
	}

	/**

	 * @param array $where
	 * @param array $set
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function updateMany( array $where, array $set, array $options = [])
	{
		return $this->m->options($options)->where($where)->set($set)->updateMany($this->collection);
	}

	/**

	 * @param array $where
	 * @param array $set
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function updateOne( array $where, array $set, array $options = [])
	{
		return $this->m->options($options)->where($where)->set($set)->updateOne($this->collection);
	}

	/**

	 * @param array $where
	 * @param array $set
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function findOneAndUpdate($update = [])
	{
		return $this->m->findOneAndUpdate($this->collection, $update);
	}

	/**

	 * @param array $where
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function deleteOne( array $where, array $options = [])
	{
		return $this->m->options($options)->where($where)->deleteOne($this->collection);
	}

	/**

	 * @param array $where
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function deleteMany( array $where, array $options = [])
	{
		return $this->m->options($options)->where($where)->deleteMany($this->collection);
	}

	/**

	 * @param array $where
	 * @param array $options
	 * @return mixed
	 * @throws \Exception
	 */
	public function count( array $where, array $options = [])
	{
		return $this->m->options($options)->where($where)->count($this->collection);
	}
}
