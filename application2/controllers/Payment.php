<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('Categories_model');
        $this->load->model('Payment_model');
        $this->load->model('Settings_model');
        $this->load->library('cart');
    }

    public function index(){
        $data['title'] = 'Pembayaran - ' . $this->Settings_model->general()["app_name"];
        $data['css'] = 'payment';
        $data['setting'] = $this->Settings_model->getSetting();
        $data['provinces'] = $this->Payment_model->getProvinces();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar');
        $this->load->view('page/payment', $data);
        $this->load->view('templates/footerv2');
    }

    public function getLocation(){
        $id = $this->input->post('id');
        $getLocation = $this->Payment_model->getCity($id);
        $list = "<option></option>";
        foreach($getLocation as $d){
            $list .= "<option value='".$d['city_id']."'>".$d['type'].' '.$d['city_name']."";
        }
        echo json_encode($list);
    }

    public function getService(){
        $jne = $this->Payment_model->getService("jne");
        $pos = $this->Payment_model->getService("pos");
        $tiki = $this->Payment_model->getService("tiki");
        $destination = $this->input->post('destination');
        $db = $this->db->get_where('cost_delivery', ['destination' => $destination])->row_array();
        $cod = $this->db->get_where('cod', ['regency_id' => $destination])->row_array();
        $list = "<option></option>";
        $cost = "";
        if($db){
            $list .= '<option value="'.$db['price'].'-antar-antar">Diantar oleh Penjual</option>';
        }
        if($cod){
            $list .= '<option value="0-cod-cod">COD (Cash of Delivery)</option>';
        }
        if(count($jne) > 0){
            foreach($jne as $s){
                $list .= '<option value="'.$s['cost'][0]['value']."-".$s['service'].'-jne">'."JNE"." ".$s['description']." (".$s['service'].")".'</option>';
            };
        }
        if(count($pos) > 0){
            foreach($pos as $s){
                $list .= '<option value="'.$s['cost'][0]['value']."-".$s['service'].'-pos">'."POS"." ".$s['description']." (".$s['service'].")".'</option>';
            };
        }
        if(count($tiki) > 0){
            foreach($tiki as $s){
                $list .= '<option value="'.$s['cost'][0]['value']."-".$s['service'].'-tiki">'."TIKI"." ".$s['description']." (".$s['service'].")".'</option>';
            };
        }
        echo json_encode($list);
    }

    public function getLocationOngkir(){
        $id = $this->input->post('id');
        $db = $this->db->get_where('cost_delivery', ['destination' => $id])->row_array();
        $list = 0;
        if($db){
            $list += $db['price'];
        }else{
            $get = $this->db->get('settings')->row_array();
            $list += $get['default_ongkir'];
        }
        echo json_encode($list);
    }

    public function succesfully(){
        if($this->cart->total() == ""){
            redirect(base_url());
        }
        $data = $this->Payment_model->succesfully();
        $list = '';
        $nom = 1;
        foreach($this->cart->contents() as $c){
            $list .= '*' . $nom . '. ' . $c['name'] . '*%0A';
            $list .= 'Jumlah: ' . $c['qty'] . '%0A';
            $list .= 'Harga (@): Rp' . number_format($c['price'],0,",",".") . '%0A';
            $list .= 'Harga Total: Rp' . number_format($c['subtotal'],0,",",".") . '%0A';
            if($c['ket'] == ""){
                $list .= 'Keterangan: -'. '%0A%0A';
            }else{
                $list .= 'Keterangan: ' . $c['ket'] . '%0A%0A';
            }
            $nom++;
        }
        $list .= 'Subtotal: *Rp' . number_format($this->cart->total(),0,",",".") . '*%0A';
        if($this->db->get("settings")->row_array()['ongkir'] != 0){
            if($data['courier'] == "antar"){
                $ongkirnya = "Diantar Penjual";
            }else if($data['courier'] == "cod"){
                $ongkirnya = "COD";
            }else{
                $ongkirnya = strtoupper($data['courier']) . ' ' . strtoupper($data['courier_service']);
            }
            $list .= 'Ongkir(' . $ongkirnya . '): *Rp' . number_format($data['ongkir'],0,",",".") . '*%0A';
        }else{
            $list .= 'Ongkir: *Rp' . number_format($data['ongkir'],0,",",".") . '*%0A';
        }
        $totalall = intval($this->cart->total()) + intval($data['ongkir']);
        $list .= 'Total: *Rp' . number_format($totalall,0,",",".") . '*%0A';
        $list .= '-----------------------%0A';
        $list .= '*Nama:*%0A';
        $list .= $data['name'] . ' (' . $data['telp'] . ')%0A%0A';
        $list .= '*Alamat:*%0A';
        $list .= $data['address'] . '%0A%0A';
        $this->cart->destroy();
        redirect("https://wa.me/6285784327759?text=Halo kak, saya mau order. %0A%0A ". $list);
    }

}
