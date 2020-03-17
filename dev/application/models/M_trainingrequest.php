<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_trainingrequest extends CI_Model
{
    var $table = 'tb_training_request';
    var $column_order = array(null, 'nik','nama_lengkap','level','jenis_pelatihan','status');
    var $column_search = array('nik','nama_lengkap','level','jenis_pelatihan','status');
    var $order = array('training_request_id' => 'desc');
 
    private function _get_datatables_query() {
        $this->db->select('p.jenis_pelatihan, tr.training_request_id, tr.nik, tr.nama_lengkap, tr.level, tr.status');
        $this->db->from('tb_training_request as tr');
        $this->db->join('tb_pelatihan as p', 'p.pelatihan_id = tr.pelatihan_id');

        //Filter
        /*
        if($this->input->post('jenis_pelatihan'))
        {
            $this->db->where('tb_pelatihan.pelatihan_id', $this->input->post('jenis_pelatihan'));
        }
        */

        $i = 0;
        foreach ($this->column_search as $item) {
            if(@$_POST['search']['value']) {
                if($i===0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }
         
        if(isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }  else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if(@$_POST['length'] != -1)
        $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all() {
        $this->db->from('tb_training_request');
        return $this->db->count_all_results();
    }
    
    public function ambil()
    {
        $this->db->select('*');
        $this->db->from('tb_training_request');
        $this->db->join('tb_pelatihan', 'tb_pelatihan.pelatihan_id = tb_training_request.pelatihan_id');
        $data = $this->db->get('');
        return $data;
    }

    public function get_by_id($id)
    {
        $this->db->from('tb_training_request');
        $this->db->join('tb_pelatihan', 'tb_pelatihan.pelatihan_id = tb_training_request.pelatihan_id');
        $this->db->where('training_request_id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }
 
    public function delete_by_id($id)
    {
        $this->db->where('training_request_id', $id);
        $this->db->delete($this->table);
    }
}