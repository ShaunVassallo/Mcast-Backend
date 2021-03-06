<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_Model extends CI_Model {

    public function add_news($title, $desc){

        $dataNews = array(
            'news_title'    =>$title,
            'news_desc'     =>$desc
        );
        $this->db->insert('tbl_news', $dataNews);
    }

    public function get_news(){
        return $this->db->select('  news_id,
                                    news_title,
                                    news_desc')
                        ->get('tbl_news');
    }

    public function edit_news($id, $title, $desc){
        return $this->db->select('  news_id,
                                    news_title,
                                    news_desc')
                        ->where($id = 'news_id')
                        ->get('tbl_news');


        $dataNews = array(
            'news_title'    =>$title,
            'news_desc'     =>$desc
        );
        $this->db->update('tbl_news', $dataNews);
    }

    public function delete_news($new){
        $this->db->where_in('news_id', $new)->delete('tbl_news');
    }

    public function get_titles($ids){
        return $this->db->select('news_title')->where_in('news_id', $ids)->get('tbl_news')->result_array();
    }

    public function get_news_by_id($id){
        return $this->db->select('  news_id,
                                    news_title,
                                    news_desc')
                        ->where('news_id', $id)
                        ->get('tbl_news')
                        ->row_array();

    }

    public function update_news($id, $name, $desc){

        $flag = FALSE;

        $data = array(
            'news_title'                 => $name,
            'news_desc'                 => $desc

        );

        $this->db   ->where('news_id', $id)
                    ->update('tbl_news', $data);

        if (!$flag)
            $flag = $this->db->affected_rows() == 1;

        return $flag;
    }


}
