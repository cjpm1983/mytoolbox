<?php
/*
* ubicacion ddel plugin en menu admin
*
* @package report_vivo
* @author Carlos Palacios <cjpm1983@gmail.com>
* @copyright  Carlos Palacios 2020
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/tablelib.php');

$url=new moodle_url('/report/vivo/orphans.php');
$prev= new moodle_url('/report/vivo/index.php');

$systemcontext= context_system::instance();//get_system_context();

$strtitle=get_string('cursoshuerfanos','report_vivo');


$PAGE->set_url($url);
$PAGE->set_context($systemcontext);

$download = optional_param('download','',PARAM_ALPHA);

$tablef = new flexible_table('uniqueaid22');

$tablef->is_downloading($download,'test','testing123');

$sql=" SELECT c.id , c.fullname AS nombre , c.category
FROM {course} as c
WHERE c.id NOT IN
(SELECT c.id
from {user} AS u, {role_assignments} AS ra ,{context} AS ct, {role} AS r
WHERE ct.contextlevel = 50 
AND ra.roleid = r.id
AND (r.shortname = 'editingteacher' OR r.shortname = 'teacher' OR r.shortname = 'manager')
AND ra.contextid = ct.id 
AND c.id = ct.instanceid
AND u.id = ra.userid)

AND c.category != 0

";
//$sql="SELECT c.id, c.fullname AS nombre, c.category FROM {course} AS c";
$sql.=" ORDER BY c.category ASC";

  $result = $DB->get_records_sql($sql); 

if (!$tablef->is_downloading()){
    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('header', 'report_vivo'));
    echo "<span><h3>".get_string('cursoslistados','report_vivo').": ".count($result)."</h3></span>";
    echo "<a href='".$prev."'  class='btn btn-success' >".get_string('regresar','report_vivo')."</a> "; 
}


            


            //$tablef = new flexible_table('uniqueaid22');

            
            $tablef->define_columns(array('id','nombre', 'category'));
            
            $tablef->define_headers(array(
                get_string('idcurso', 'report_vivo'), 
                get_string('name', 'report_vivo'), 
                get_string('facultad', 'report_vivo'),
            ));

            //$tablef->sortable(true,'nombre',SORT_DESC);

            //$tablef-collapsible(false);
            
            $tablef->define_baseurl($url);



                $tablef->setup();
//print_r($result);
                foreach($result as $r){
                    if ($r->category != 0){
                        $facultad = getFacultad($r->id, $r->category);
                        $tablef->add_data(array(
                            $r->id,
                            $r->nombre,
                            $facultad
                            )
                        );
                    }
                   // echo $r->nombre.", ";
                    }
//getFacultad($r->id,$r->category)
                $tablef->set_control_variables(
                    array(
                        TABLE_VAR_SORT=>'ssort',
                        TABLE_VAR_IFIRST=>'sifirst',
                        TABLE_VAR_ILAST=>'silast',
                        TABLE_VAR_PAGE=>'sPAGE',
                    )
                );

            // $tablef->out(40,true);

            //$tablef->print_html();
            $tablef->finish_output();



        
        //echo "</div>";
    //}

//}


if (!$tablef->is_downloading()){
    echo $OUTPUT->footer();
}

function getFacultad($cid, $ccat){
    global $DB; 
        $category =$DB->get_record('course_categories', array("id" => $ccat));
        // categoryparent
        $parentcatids = explode("/",trim( $category->path, "/"));
        //get the name of the parent category
        $sql = "SELECT id, name FROM {course_categories} WHERE id in (".implode(",", $parentcatids).")"; 
        $parentcatsnames = $DB->get_records_sql($sql);
         //0 = parent or 1 = undercategory
        $headline = $parentcatsnames[$parentcatids[0]];
    return $headline->name;
}


