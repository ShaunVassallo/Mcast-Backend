<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courses_Model extends CI_Model {

    # Get the list of courses from the db
    public function all(){

        $result = $this->db ->select('course_id, course_name')
                            ->get('tbl_courses')
                            ->result_array();

        $array = array();
        foreach ($result as $row)
        {
            $array[$row['course_id']] = $row['course_name'];
        }

        return $array;

    }

    # Deletes a user from the database
    public function delete_user($user){
        $this->db->where_in('id_login', $user)->delete('tbl_login');
    }

    public function add_student($id_card, $email, $name, $surname, $course, $link) {

        $salt 		= bin2hex($this->encryption->create_key(8));

        //an insert query
        //inset into tbl_users(cols) values (cols)

        $dataLogin = array(
            'user_id'               => $id_card,
            'email_login'           => $email,
            'pass_login'            => password_hash($salt.$id_card, CRYPT_BLOWFISH),
            'salt_login'            => strrev($salt)
        );
        $this->db->insert('tbl_login', $dataLogin);
        //gives us whatever the PK value is last
        //return $this->db->insert_id();
        $id = $this->db->insert_id();

        $dataUser = array(
            'tbl_login_id_login'    => $id,
            'user_name'             => $name,
            'user_surname'          => $surname
        );
        $this->db->insert('tbl_users', $dataUser);


        $data = array(
            'tbl_login_id_login'    => $id,
            'std_link'                  => $link,
            'tbl_courses_course_id' => $course
        );
        $this->db->insert('tbl_std', $data);
    }


    public function get_students(){
        return $this->db->select('  tbl_login.id_login,
                                    tbl_login.user_id,
                                    tbl_users.user_name,
                                    tbl_users.user_surname,
                                    tbl_login.email_login,
                                    tbl_courses.course_name,
                                    tbl_courses.course_lvl,
                                    tbl_std.std_link')
                        ->join('tbl_users',     'tbl_users.tbl_login_id_login   = tbl_login.id_login',        'left')
                        ->join('tbl_std',       'tbl_std.tbl_login_id_login     = tbl_login.id_login',        'left')
                        ->join('tbl_courses',   'tbl_std.tbl_courses_course_id  = tbl_courses.course_id',     'left')
                        ->where('tbl_login.tbl_roles_id = 3')
                        ->get('tbl_login');
    }

    public function get_user($id){
        //return $this->db->get_where('tbl_users', array('id' => $id));

        //run a query and return the row immediately
        return $this->db->select('  tbl_login.id_login,
                                    tbl_login.user_id,
                                    tbl_users.user_name,
                                    tbl_users.user_surname,
                                    tbl_login.email_login,
                                    tbl_courses.course_name,
                                    tbl_courses.course_lvl,
                                    tbl_std.std_link')
                        ->join('tbl_users',     'tbl_users.tbl_login_id_login   = tbl_login.id_login',        'left')
                        ->join('tbl_std',       'tbl_std.tbl_login_id_login     = tbl_login.id_login',        'left')
                        ->join('tbl_courses',   'tbl_std.tbl_courses_course_id  = tbl_courses.course_id',     'left')
                        ->where('tbl_login.id_login', $id)
                        ->get('tbl_login')
                        ->row_array();


    }

    public function update_user($login_id, $id_card, $email, $name, $surname, $course, $link){


        $flag = FALSE;
        $salt 		= bin2hex($this->encryption->create_key(8));

        //an insert query
        //inset into tbl_users(cols) values (cols)

        $dataLogin = array(
            'user_id'               => $id_card,
            'email_login'           => $email,
            'pass_login'            => password_hash($salt.$id_card, CRYPT_BLOWFISH),
            'salt_login'            => strrev($salt)
        );

        $this->db   ->where('id_login', $login_id)
                    ->update('tbl_login', $dataLogin);

        if (!$flag)
            $flag = $this->db->affected_rows() == 1;


        $dataUser = array(
            'user_name'             => $name,
            'user_surname'          => $surname
        );
        $this->db   ->where('tbl_login_id_login', $login_id)
                    ->update('tbl_users', $dataUser);

        if (!$flag)
            $flag = $this->db->affected_rows() == 1;


        $data = array(
            'std_link'              => $link,
            'tbl_courses_course_id' => $course
        );
        $this->db   -> where('tbl_login_id_login', $login_id)
                    ->update('tbl_std', $data);

        if (!$flag)
            $flag = $this->db->affected_rows() == 1;

        return $flag;
    }




}
