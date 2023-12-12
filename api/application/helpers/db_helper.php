<?php
function check_multi_exists($table,$data_array,$check_val)
{
  $ifdb =& get_instance();
  foreach($data_array as $key=>$val)
  {
     if(in_array($key,$check_val))
     {
      $ifdb->db->where($key,$val);
     }
  }
  $q = $ifdb->db->get($table);
  if($q->num_rows()>0)
  {
    return 'yes';
  }
  return 'no';
}

function get_column($table,$col_sname,$col_sval,$col_gname,$rval='n/a')
{
  $ifdb =& get_instance();
  $ifdb->db->where($col_sname,$col_sval);
  $q = $ifdb->db->get($table);
  $col_gval =$rval;
  if($q->num_rows()==1)
  {
      $r = $q->row();
      $col_gval = $r->$col_gname;
  }
  return $col_gval;
}
