<?php
function doError($code, $str) {
  set_status_header($code);
  echo $str ;
  exit();
}

/**
 * exits if there is no access.
 */
function checkAccess() {
  global $page;

  $picid = '';
  $query = 'SELECT id FROM '.IMAGES_TABLE.' WHERE path=\''.$page['src_location'].'\';';
  $result = pwg_query($query);
  if (!is_object($result)) {
    header('Location:'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
  }
  if (($row = pwg_db_fetch_assoc($result))) {
    if (isset($row['id'])) {
      $picid = $row['id'];
    } else {
      doError(404, 'Requested id not found');
    }
  } else {
    doError(404, 'Requested id not found');
  }
  
  $query = 'SELECT id FROM '.CATEGORIES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON category_id = id WHERE image_id = '.$picid.' '.get_sql_condition_FandF(
    array(
        'forbidden_categories' => 'category_id',
        'forbidden_images' => 'image_id',
      ),
    '    AND'
    ).'
    LIMIT 1;';
  if (pwg_db_num_rows(pwg_query($query)) < 1) {
    doError(401, 'Access denied');
  }
}