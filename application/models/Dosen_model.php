<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dosen_model extends CI_Model {

	public function getNilaiMahasiswaById($id)
	{
		return $this->db->get_where('nilai_mahasiswa', ['id' => $id])->row_array();
	}
	public function getNilaiMataKuliahById($id)
	{
		return $this->db->get_where('nilai_mata_kuliah', ['id' => $id])->row_array();
	}
	public function getSubNilaiMataKuliahById($id)
	{
		return $this->db->get_where('sub_nilai_mata_kuliah', ['id' => $id])->row_array();
	}

	// Fungsi untuk melakukan proses upload file
	public function upload_file($filename){
		
		$config['upload_path'] = './excel/';
		$config['allowed_types'] = 'xlsx';
		$config['max_size']	= '4048';
		$config['overwrite'] = true;
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		if($this->upload->do_upload('file')){ // Lakukan upload dan Cek jika proses upload berhasil
			// Jika berhasil :
			$return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
			return $return;
		}else{
			// Jika gagal :
			$return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
			return $return;
		}
	}

	public function insert_excelSubNilaiMataKuliah($data)
	{
		$this->db->insert_batch('sub_nilai_mata_kuliah', $data);
	}

	public function getInfoMahasiswa($limit, $start, $id_kelas = null, $keyword = null){
		$data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
		$dosen = $this->db->get_where('dosen', ['id_user' => $data['user']['id']])->row_array(); 

		$this->db->select('*');
		$this->db->from('user');
		$this->db->join('mahasiswa', 'user.id=mahasiswa.id_user');
		$this->db->join('nilai_mahasiswa', 'mahasiswa.id = nilai_mahasiswa.id_mahasiswa');
		$this->db->join('kelas', 'kelas.id=mahasiswa.id_kelas');
		if ($data['user']['role_id']!=1) {
			$this->db->where('id_dosen_wali', $dosen['id']);
		}
		if ($id_kelas) {
			$this->db->where('id_kelas', $id_kelas);
		}
		$this->db->limit($limit, $start);

		return $this->db->get()->result_array();
	}

	public function countAllMahasiswa($id_kelas = null, $keyword = null){
		$data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
		$dosen = $this->db->get_where('dosen', ['id_user' => $data['user']['id']])->row_array(); 

		$this->db->select('*');
		$this->db->from('user');
		$this->db->join('mahasiswa', 'user.id=mahasiswa.id_user');
		$this->db->join('nilai_mahasiswa', 'mahasiswa.id = nilai_mahasiswa.id_mahasiswa');
		$this->db->join('kelas', 'kelas.id=mahasiswa.id_kelas');
		if ($data['user']['role_id']!=1) {
			$this->db->where('id_dosen_wali', $dosen['id']);
		}
		if ($id_kelas) {
			$this->db->where('id_kelas', $id_kelas);
		}

		return $this->db->get()->num_rows();
	}
}