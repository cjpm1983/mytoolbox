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

$url=new moodle_url('/report/vivo/diagnostic.php');
$prev= new moodle_url('/report/vivo/index.php');

$systemcontext= context_system::instance();//get_system_context();

$strtitle=get_string('cursosdiagnostico','report_vivo');


$PAGE->set_url($url);
$PAGE->set_context($systemcontext);

$download = optional_param('download','',PARAM_ALPHA);

$tablef = new flexible_table('uniqueaid22');



$libro="Reporte cursos de Moodle";
$hoja="Listado";

$tablef->is_downloading($download,$libro,$hoja);



$sql = "
SELECT DISTINCT
        c.fullname AS nombre,
        c.id AS id,
        c.category,
        COUNT(m.id) AS cuentamodulos,        
FROM
        {course} AS c, 
        {course_modules} AS cm, 
        {modules} AS m        
        
WHERE
        (c.id <> 1) 
        AND (m.name <> 'forum')
        AND (m.name <> 'label')

        AND (c.id = cm.course)
        AND (cm.module = m.id)
GROUP BY
        c.id

";

$sqlFolders = "SELECT c.id as curso, count(m.id) as cantfolder from {course} AS c, {course_modules} AS cm, 
{modules} AS m WHERE (m.name='folder') AND (c.id = cm.course)
AND (cm.module = m.id) GROUP BY curso";

//(SELECT cc.id, cc.path from {course_categories} as cc,{course} as c WHERE (cc.id=c.category)) AS path

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
    echo "<h5>Se buscarán en los cursos de Moodle los módulos siguientes: assign, assignment, book, chat, choice, data, feedback, folder, glossary, h5pactivity, imscp, lesson, lti, page, quiz, resource, scorm, survey,url ,wiki y workshop (Se exceptúan label y forum; el primero es texto plano y el segundo se presenta por defecto en los cursos creados.)</h5>";
    echo "<h6>*Se listan los cursos con al menos dos de éstos recursos </h6>";
    echo "<br>";
    ?>
    <!--Listando las Facultades-->        
    <?php 
        $sqlf= "
            Select c.id,c.name from {course_categories} AS c where (parent = 0) ORDER BY c.name
        ";
        $facs = $DB->get_records_sql($sqlf);

        if (isset($_GET['facultad'])){
        
            // $factual = array_filter($facs,function($el){
            //     if ($el->id == $_GET['facultad'])
            //     return true;
            //     return false;
            // });

            echo "Listando Facultad: ".$_GET['facultad'];
            }else{
                echo "Listando todas las facultades";
            }
    ?>    
    

    <form action="" method="get">
        <select name="facultad" id="facultad" class="form-control custom-select mr-1">
            <?php 
            
            if (isset($_GET['facultad'])){
                $defecto = $_GET['facultad'];
            }else
                $_GET['facultad'] = $facs[0]->name;
            
            foreach ($facs as $f) { ?>
                <option value="<?php echo $f->name;?>" 
                <?php if ($f->name==$_GET['facultad']) echo "selected"; ?> 
                >
                <?php echo $f->name?>
                </option>
            <?php } ?>
        </select>
        <input type="submit" value="Listar">
    </form>

    
    <?php
    }




            //$tablef->sortable(true,'nombre',SORT_DESC);

            //$tablef-collapsible(false);
            
            $tablef->define_baseurl($url);
            $tablef->define_columns(array('id','n','nnn','nnna','nnnnaaa','nnnnaaaaaaaa','nnnsss','nnnddssn','xxx','dddf'));

            $tablef->define_headers(array('Facultad','Pregrado','Modalidad','Carrera','Año','Semestre','Curso','ID Curso','Tiene recursos','Cantidad'));


            $tablef->setup();
            //Hay que ver elarbol de categorias mayor y en dependencia poner columnas a la tabla
            $MayorArbolSize = 0;
            
            foreach($result as $r){
                if ($r->category != 0){
                    $columnas = getCategorias($r->id, $r->category);
                    if ($columnas[1] != 'Pregrado'){ //Pues solo listamos pregrado
                        continue;
                    }
                    //cuando es por facultad
                    if (isset($_GET["facultad"]))
                        if ($columnas[0] != $_GET["facultad"])
                            continue;

                    array_push($columnas,$r->nombre,$r->id);  

                    //Si tiene recursos e interactividad
                    ($r->cuentamodulos > 1)?@array_push($columnas,'Si'):@array_push($columnas,'No');
                    array_push($columnas,$r->cuentamodulos);
                    array_push($columnas,$r->cantfolder);
                    

                    $tablef->add_data($columnas);
                    if (count($columnas)>$MayorArbolSize){
                        $MayorArbolSize=count($columnas);
                    }
                }
                // echo $r->nombre.", ";
                }
                $encabezados = [];
                for ($i=1; $i <=$MayorArbolSize ; $i++) { 
                    array_push($encabezados,'Categoria '.$i);
                }

                array_push($encabezados,'id');
                array_push($encabezados,'nombre');
                
                //$tablef->define_columns($encabezados);
                // $tablef->define_columns(array('','','','','','','',''));

                // $tablef->define_headers($encabezados);

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

function getCategorias($cid, $ccat){
    global $DB; 
        $category =$DB->get_record('course_categories', array("id" => $ccat));
        // categoryparent
        $parentcatids = explode("/",trim( $category->path, "/"));
        //get the name of the parent category
        $sql = "SELECT id, name FROM {course_categories} WHERE id in (".implode(",", $parentcatids).")"; 
        $parentcatsnames = $DB->get_records_sql($sql);
         //0 = parent or 1 = undercategory
         //print_r($parentcatsnames);
        $headline = [];
        for ($i=0; $i < (count($parentcatids)); $i++) { 
            array_push($headline,$parentcatsnames[$parentcatids[$i]]->name); 
        }

       //$headline = $parentcatsnames[$parentcatids[0]];
       
    return $headline;
}


