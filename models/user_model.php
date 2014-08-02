<?php

/**
 * @package Basic_Auth
 * @version 1.0
 * @author Carey Dayrit <carey.dayrit@gmail.com>
 */
class User_model extends CI_Model {

	protected $table_name = 'users';
	protected $primary_column = 'id';
	public $search_column = 'lastname';

	/**
	 * Method: __construct
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function get_all($q, $cur_page = 0, $per_page = 10)
	{
		if (!empty($q))
		{
			$this->db->like($this->search_column, $q);
		}

		$query = $this->db->limit($per_page, $cur_page)
				->get();

		return $query->result_array();
	}

	public function num_rows($q)
	{
		$this->db->select('*')
				->from($this->table_name);


		if (!empty($q))
		{
			$this->db->like($this->search_column, $q);
		}
		$query = $this->db->get();

		return $query->num_rows();
	}

	/**
	 * Method: create
	 * @param array $data
	 * @return integer|boolean Returns the table insert id upon success, FALSE upon failure.
	 */
	public function create($data = NULL)
	{
		$query = $this->db->insert($this->table_name, $data);

		if ($query)
		{
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Method: read
	 * @param integer $primary_id
	 * @return array
	 */
	public function read($primary_id = NULL)
	{
		$query = $this->db->select('*')
				->from($this->table_name)
				->where($this->primary_column, $primary_id)
				->get();

		return $query->row_array();
	}

	/**
	 * Method: update
	 * @param array $data
	 * @param integer $primary_id
	 * @return boolean
	 */
	public function update($data = NULL, $primary_id = NULL)
	{
		$query = $this->where($this->primary_colum, $primary_id)
				->update($this->table_name, $data);

		return $query;
	}

}
