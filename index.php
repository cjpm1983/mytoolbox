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
require_once($CFG->dirroot.'/report/vivo/form/vivo_form.php');
require_once($CFG->dirroot.'/report/vivo/form/vivo_form2.php');
require_once($CFG->dirroot.'/report/vivo/form/vivo_form3.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/tablelib.php');

$url=new moodle_url('/report/vivo/index.php');

$systemcontext= context_system::instance();//get_system_context();

$strtitle=get_string('title', 'report_vivo');

$PAGE->set_url($url);
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_pagelayout('report');
$PAGE->set_heading($strtitle);

$mform = new vivo_form();//si hay parametros se le pasan luego de una coma en un arreglo ej array('courses'=>$courses)
$mform2 = new vivo_form2();
$mform3 = new vivo_form3();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header', 'report_vivo'));
$urlOrphan=new moodle_url('/report/vivo/orphans.php');
$ontology=new moodle_url('/report/vivo/ontology.php');

?>

<br>
<ul class='menuvivo'>
    <li>
        <a href='<?php echo $ontology ?>'  class='btn btn-success'><?php echo get_string('generate_ontology','report_vivo'); ?></a>    
    </li>
    <li>
        <a href='<?php echo $urlOrphan ?>'  class='btn btn-success' ><?php echo get_string('cursoshuerfanos','report_vivo'); ?></a>    
    </li>
    <li>
        <button onclick='mform1()' class='btn btn-success' ><?php echo get_string('form1header','report_vivo') ?></button>    
    </li>
    <li>
    <button onclick='mform2()'  class='btn btn-success' ><?php echo get_string('form2header','report_vivo') ?></button>    
    </li>
</ul>

<style>
    .menuvivo{
        display:flex;
        flex-wrap: wrap;
    }
    .menuvivo > li > a,.menuvivo > li > a:hover{
        color:white;
    }
    .menuvivo  {
        list-style-type: none;
    }
</style>


<?php

$mform->display();
$mform2->display();

//sembrando referencias de los seeders
/*
for ($i=1; $i < 2000; $i++) { 
    try {
        echo "listo ".check_enrol('curso#'.$i, 27+$i, 3, $enrolmethod = 'manual')."   "."curso".$i." - usuaurio".$i."<br>" ;
    } catch (\Throwable $th) {
        throw $th;
    }
    
}

*/



///fin de la siembra

if($fromform=$mform->get_data()){
    echo "<div class='mform1'>";
    
    if ($fromform->campo && $fromform->filtro){
    
        //code...
        echo '<h3>'.get_string('salida', 'report_vivo').'</h3>';

        $campo=$fromform->campo;
    
        $parametros=array('param1'=>$campo) ;
        $registro_user_info_fields = $DB->get_record_sql('SELECT * FROM {user_info_field} WHERE shortname = :param1',$parametros );
    
        $parametros1=array('param1'=>$fromform->filtro,'param2'=>$registro_user_info_fields->id) ;
        $registro_user_info_data = $DB->get_record_sql('SELECT * FROM {user_info_data} WHERE data = :param1 AND fieldid=:param2',$parametros1 );
        $usuario=@$DB->get_record('user', ['id' => $registro_user_info_data->userid] );
		
        if($usuario){

		//Datos del usuario
            echo '<table class="table">';
                echo '<tr>';
                    echo '<td>';
                        echo "<b>ID</b>";    
                    echo '</td>';    
                    echo '<td>';
                        echo $usuario->id."";
                    echo '</td>';    
                echo '</tr>';
                echo '<tr>';
                    echo '<td>';
                        echo "<b>".get_string('fullname', 'report_vivo')."</b>";    
                    echo '</td>';    
                    echo '<td>';
                        echo $usuario->firstname." ".$usuario->lastname;
                    echo '</td>';    
                echo '</tr>';
                echo '<tr>';
                    echo '<td>';
                        echo "<b>".get_string('email', 'report_vivo')."</b>";    
                    echo '</td>';    
                    echo '<td>';
                        echo $usuario->email." ";
                    echo '</td>';    
                echo '</tr>';
            echo '</table>';
		
		
		//sql para obtener los cursos
		$sql="SELECT c.id, c.fullname, c.startdate, c.enddate, u.lastname, r.shortname, ct.path, c.category 
		
		FROM {course} AS c
		JOIN {context} AS ct ON c.id = ct.instanceid
		JOIN {role_assignments} AS ra ON ra.contextid = ct.id
		JOIN {user} AS u ON u.id = ra.userid
		JOIN {role} AS r ON r.id = ra.roleid
		WHERE r.shortname = 'editingteacher' AND u.id = ?
		";

		
		$result = $DB->get_records_sql($sql, array($usuario->id)); 
		
        echo "<br>";
        

		echo  '<h3>'.get_string('profesoren', 'report_vivo')."</h3>";
        
        
        //---------------------------------------------------
        $table = new html_table();
        $table->head = array(get_string('idcurso', 'report_vivo'), get_string('name', 'report_vivo'), get_string('facultad', 'report_vivo'),get_string('inicio', 'report_vivo'),get_string('culminacion', 'report_vivo'));

        foreach($result as $registro){
            $table->data[] = array($registro->id,
            $registro->fullname, 
            getFacultad($registro->id,$registro->category),
            userdate($registro->startdate),
            userdate($registro->enddate)        
        );
        }

        echo html_writer::table($table);
        //-------------------------------------------
        
    
        
        }else{
            echo "No hay resultados";
        }
       
    }else{
        echo get_string('llenecorrecto', 'report_vivo');
    }
    echo "</div>";
}

if($fromform=$mform2->get_data()){
    echo "<div class='mform2'>";
    if ($fromform->campo3){
    
        //code...
        echo '<h3>'.get_string('salida', 'report_vivo').'</h3>';

        $campo=$fromform->campo3;
    
        //contenidox
        $curso=obtenerCursosDadoId($campo);

		//Datos del curso
        echo '<table class="table">';
        echo '<tr>';
            echo '<td>';
                echo "<b>ID</b>";    
            echo '</td>';    
            echo '<td>';
                echo $curso->id."";
            echo '</td>';    
        echo '</tr>';
        echo '<tr>';
            echo '<td>';
                echo "<b>".get_string('name', 'report_vivo')."</b>";    
            echo '</td>';    
            echo '<td>';
                echo $curso->fullname."";
            echo '</td>';    
        echo '</tr>';
        echo '<tr>';
            echo '<td>';
                echo "<b>".get_string('facultad', 'report_vivo')."</b>";    
            echo '</td>';    
            echo '<td>';
                echo getFacultad($curso->id,$curso->category)." ";
            echo '</td>';    
        echo '</tr>';

        echo '<tr>';
            echo '<td>';
                echo "<b>".get_string('inicio', 'report_vivo')."</b>";    
            echo '</td>';    
            echo '<td>';
                echo userdate($curso->startdate)." ";
            echo '</td>';    
        echo '</tr>';

        echo '<tr>';
            echo '<td>';
                echo "<b>".get_string('culminacion', 'report_vivo')."</b>";    
            echo '</td>';    
            echo '<td>';
                echo userdate($curso->enddate)." ";
            echo '</td>';    
        echo '</tr>';

    echo '</table>';

    

    //listar los profes
    echo "<br>";
    echo '<h3>'.get_string('profesoresdelcurso', 'report_vivo').'</h3>';

        
    $table = new html_table();
    $table->head = array(get_string('idcurso', 'report_vivo'), get_string('fullname', 'report_vivo'), get_string('email', 'report_vivo'));

    $profesores=obtenerProfesoresDeUnCurso($campo);
    foreach($profesores as $registro){
        $table->data[] = array($registro->id,$registro->firstname." ".$registro->lastname,$registro->email);
    }

    echo html_writer::table($table);

    }
    else{
        echo get_string('llenecorrecto', 'report_vivo');
    }
    echo "</div>";
}


echo "<div style='width:100%;text-align:right;color:#cccccc'>Palacios - Copyright 2020</div>";
echo $OUTPUT->footer();




function obtenerCursosDadoId($id){
    global $DB;

    $course=@$DB->get_record('course', ['id' => $id] );

    return $course;

}

function obtenerProfesoresDeUnCurso($idcurso){
    
    //profesores=array();
    global $DB;
    
    //sql para obtener los cursos
    $sql="SELECT u.id, u.username, u.firstname, u.lastname, u.email 

    FROM {course} AS c
    JOIN {context} AS ct ON c.id = ct.instanceid
    JOIN {role_assignments} AS ra ON ra.contextid = ct.id
    JOIN {user} AS u ON u.id = ra.userid
    JOIN {role} AS r ON r.id = ra.roleid
    WHERE r.shortname = 'editingteacher' AND c.id = ?
    ";


    @$ProfesCrudo = $DB->get_records_sql($sql, array($idcurso)); 
    return $ProfesCrudo;
}

function getFacultad($cid,$ccat){
    global $DB; 
        $category =$DB->get_record('course_categories', array("id" => $ccat));
        // categoryparent
        $parentcatids = explode("/",trim( $category->path, "/"));
        //get the name of the parent category
        $sql = "SELECT id, name FROM {course_categories} WHERE id in (".implode(",", $parentcatids).")"; $parentcatsnames = $DB->get_records_sql($sql);
         //0 = parent or 1 = undercategory
        $headline = $parentcatsnames[$parentcatids[0]];
    return $headline->name;
}

//funcion mia para seeder de datos de prueba

function check_enrol($shortname, $userid, $roleid, $enrolmethod = 'manual') { 
	global $DB; 

    //$roleid = $DB->get_field('role', 'id', array('shortname' => $shortame));
	

    $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST); 
	$course = $DB->get_record('course', array('shortname' => $shortname), '*', MUST_EXIST); 
    $context = context_course::instance($course->id); 
    
    
	if (!is_enrolled($context, $user)) { 
		$enrol = enrol_get_plugin($enrolmethod); 
		if ($enrol === null) { 
			return false; 
		} 
		$instances = enrol_get_instances($course->id, true); 
		$manualinstance = null; 
		foreach ($instances as $instance) { 
			if ($instance->name == $enrolmethod) { 
				$manualinstance = $instance; break; 
			} 
		} 
		if ($manualinstance !== null) { 
			$instanceid = $enrol->add_default_instance($course); 
			if ($instanceid === null) { 
				$instanceid = $enrol->add_instance($course); 
			} 
		    $instance = $DB->get_record('enrol', array('id' => $instanceid)); 
	    } 
	$enrol->enrol_user($instance, $userid, $roleid); 
	} 
return true; 
} 
	

//FUNCIONES JAVASCRIPT
echo "
<script>

    window.addEventListener('load',escondeTodos,false);
    
    function escondeTodos(){
        $('#mform1').hide();
        $('#mform2').hide();
    }

    function mform1(){
        $('#mform1').show();
        $('#mform2').hide();
        $('.mform1').show();
        $('.mform2').hide();
    }

    
    function mform2(){
        $('#mform1').hide();
        $('#mform2').show();
        $('.mform1').hide();
        $('.mform2').show();
    }

    
    function mform3(){
        $('#mform1').hide();
        $('#mform2').hide();
        $('.mform1').hide();
        $('.mform2').hide();
    }


</script>
";

