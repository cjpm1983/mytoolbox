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

$url=new moodle_url('/report/vivo/backups.php');
$prev= new moodle_url('/report/vivo/index.php');

$systemcontext= context_system::instance();//get_system_context();

$strtitle="Backups";


$PAGE->set_url($url);
$PAGE->set_context($systemcontext);

$download = optional_param('download','',PARAM_ALPHA);

$tablef = new flexible_table('uniqueaid22');



$libro="Reporte Backups";
$hoja="Listado";

$tablef->is_downloading($download,$libro,$hoja);


//Borra fichero
if (isset($_GET['del']) && (is_siteadmin())){
    $hash = $_GET['del'];

    $folder1=$hash[0]."".$hash[1];
    $folder2=$hash[2]."".$hash[3];

    $camino = $CFG->dataroot."/filedir/".$folder1."/".$folder2."/".$hash;
    $resultado = `rm $camino`;

    //echo $camino;

    //$sql = "delete from mdl_files where contenthash = '".$hash."'";

    $result = $DB->delete_records('files',array('contenthash'=>$hash)); 

    header('location: /report/vivo/backups.php');
    //rm -R /var/www/moodledata/trashdir/*
}



$sql = "
    select distinct
        
        files.id as id,
        contextid as contexto,
        c.fullname as curso,
        c.category as categoria,
        filename,
        instanceid as idcurso, 
        contenthash,
        CAST(ROUND((filesize/1024/1024),2) as CHAR) AS tamanio 

    from 
        {files} as files,
        {context} as context, 
        {course} as c
    where 
        filename like '%mbz' and 
        contextid = context.id and
        c.id = instanceid
    ";

    $sql.=" ORDER BY tamanio DESC";

    $result = $DB->get_records_sql($sql); 

    //Borra Todo
if (isset($_GET['flush']) && (is_siteadmin())){

    foreach($result as $r){

        $hashi = $r->contenthash;
        $folder1=$hashi[0]."".$hashi[1];
        $folder2=$hashi[2]."".$hashi[3];
    
        $camino = $CFG->dataroot."/filedir/".$folder1."/".$folder2."/".$hashi;
        $resultado = `rm $camino`;
    
        $result = $DB->delete_records('files',array('contenthash'=>$hashi)); 
    
        header('location: /report/vivo/backups.php');
        
    }
}

//Borra Todo
if (isset(($_GET['trashdir'])) && (is_siteadmin())){

            $camino = $CFG->dataroot.'/trashdir/*';
            $resultado = `rm -R $camino`;

            $camino = $CFG->dataroot.'/temp/backup/*';
            $resultado = `rm -R $camino`;
        
            header('location: /report/vivo/backups.php');
            
        
}

    








if (!$tablef->is_downloading()){

    $espacio = 0;
    foreach ($result as $r){
        $espacio += $r->tamanio;
    }

    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('header', 'report_vivo'));
    echo "<span><h3>".get_string('ficherosencontrados','report_vivo').": ".count($result)."</h3></span>";
    echo "<span><h3>".get_string('espacioocupado','report_vivo').": ".$espacio."&nbsp;Mb</h3></span>";
    echo "<a href='".$prev."'  class='btn btn-success' >".get_string('regresar','report_vivo')."</a> "; 
    echo "<a href='#'  class='btn btn-warning' onclick='eliminar()'>".get_string('eliminartodos','report_vivo')."</a> "; 
    echo "<a href='#'  class='btn btn-warning' onclick='trashdir()'>".get_string('limpiar','report_vivo')."</a> "; 

    echo '<div class="my-auto">' . $OUTPUT->lang_menu(true) . '</div>';
    echo $OUTPUT->search_box();

    ?>
    <script>
    function eliminar(){

        <?php if (is_siteadmin()) {?>
            r = confirm("Confirme que desea eliminar todos los archivos de respaldos de cursos del sitio. Esta accion es irreversible.");
            if (r){
            location.href = "/report/vivo/backups.php?flush=true"
        }
        <?php } else {?>
            alert("Solo administradores pueden ejecutar esta accion.")
        <?php } ?>
        

    }
    function trashdir(){

        <?php if (is_siteadmin()) {?>
            r = confirm("En trashdir se almacenan archivos huerfanos mediante cron, \
        alli permanecen 4 dias y luego son eliminados, en ocaciones se acumulan \
        algunos sin borrar, ocupando espacio. Sucede similar con temp/backup que \
        se utiliza en el momento de retaurar salvas, Confirme para limpiar ambos directorios.");
        if (r){
            location.href = "/report/vivo/backups.php?trashdir=true"
        }
        <?php } else {?>
            alert("Solo administradores pueden ejecutar esta accion.")
        <?php } ?>



 
    }
    </script>
    <?php
    echo "<br>";
    
}




            //$tablef->sortable(true,'tamanio',SORT_DESC);

            //$tablef-collapsible(false);
            
            $tablef->define_baseurl($url);
            $tablef->define_columns(array('a','n','nnn','nnna','bv','bb'));
            

            $tablef->define_headers(array(get_string('tamanio','report_vivo'),get_string('curso','report_vivo'),'Backup',get_string('ubicacion','report_vivo'),get_string('fichero','report_vivo'),get_string('borrar','report_vivo')));


            $tablef->setup();
            
            foreach($result as $r){
                    $ubicacion = "";
                    foreach (getCategorias($r->categoria) as $nivel) {
                        $ubicacion = $ubicacion."/";
                    }

                    $columnas = array();        
                    
                    $enlace = $CFG->wwwroot."/backup/restorefile.php?contextid=".$r->contexto;
                    $borrar = "<a onclick='verpermisos()' href='?del=".$r->contenthash."' >".get_string('borrar','report_vivo')."</a>";
                    array_push($columnas,$r->tamanio);  
                    array_push($columnas,$r->curso);  
                    array_push($columnas,$r->filename);  
                    array_push($columnas,"<a target='_blank' href='".$enlace."' >".get_string('ubicacion','report_vivo')."</a>");  
                    array_push($columnas,$r->contenthash);
                    array_push($columnas,$borrar);  


                    $tablef->add_data($columnas);
               
                }

                ?>
                <script>
                function verpermisos(){
                    <?php if (!is_siteadmin()) {?>
                        alert("Solo administradores pueden ejecutar esta accion.")
                    <?php } ?>

                }
                </script>
                <?php

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


function getCategorias($ccat){
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



