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
require_once($CFG->dirroot . '/user/profile/lib.php');

require_once($CFG->dirroot . '/report/vivo/form/draft_form.php');
require_once($CFG->libdir . '/tablelib.php');

$url = new moodle_url('/report/vivo/drafts.php');
$prev = new moodle_url('/report/vivo/index.php');

$systemcontext = context_system::instance(); //get_system_context();

$strtitle = "DRAFTS";


$PAGE->set_url($url);
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->set_pagelayout('report');



//Borra fichero Antes de lanzar Header
if (isset($_GET['del']) && (is_siteadmin())) {
    $id = $_GET['del'];

    $fs = get_file_storage();
    $storedfile = $fs->get_file_by_id($id);

    $storedfile->delete();

    header('location: /report/vivo/drafts.php');
}

if (isset(($_GET['trashdir'])) && (is_siteadmin())) {

    $camino = $CFG->dataroot . '/trashdir/*';
    $resultado = `rm -R $camino`;

    header('location: /report/vivo/drafts.php');
}





//Borra Todo
if (isset($_GET['flush']) && isset($_GET['iduf']) && (is_siteadmin())) {


    $sql = "
            select 
            id
            from mdl_files where filearea = 'draft' and userid=" . $_GET['iduf']. ";  ";
        // $sql.=" ORDER BY tamanio DESC";

        $result = $DB->get_records_sql($sql);

    $fs = get_file_storage();

    foreach ($result as $r) {



        $id = $r->id;

        $storedfile = $fs->get_file_by_id($id);

        $storedfile->delete();

        header('location: /report/vivo/drafts.php');
    }
}


echo $OUTPUT->header();
echo $OUTPUT->heading("List draft Files");



$mform = new draft_form();

$download = optional_param('download', '', PARAM_ALPHA);

$tablef = new flexible_table('uniqueaid22');



$libro = "Reporte Drafts";
$hoja = "Listado";

//Inicializamos las variables de consulta a la base de datos
$sql = "";
$result;

$tablef->is_downloading($download, $libro, $hoja);
















if (!$tablef->is_downloading()) {



?>
    <script>
        function trashdir() {

            <?php if (is_siteadmin()) { ?>
                r = confirm("En trashdir se almacenan archivos huerfanos mediante cron, \
alli permanecen 4 dias y luego son eliminados, en ocaciones se acumulan \
algunos sin borrar, ocupando espacio. Confirme para limpiar.");
                if (r) {
                    location.href = "/report/vivo/drafts.php?trashdir=true"
                }
            <?php } else { ?>
                alert("Solo administradores pueden ejecutar esta accion.")
            <?php } ?>




        }
    </script>
    <?php
    echo "<br>";
}


//Muestra elformulario del id
$mform->display();

echo "<a href='" . $prev . "'  class='btn btn-success' >" . get_string('regresar', 'report_vivo') . "</a> ";
//Si el formulario envio





if ($fromform = $mform->get_data()) {
    echo "<div class='mform1'>";

    if ($fromform->useridf) {


        $sql = "
            select 
            id,
            filename,
            filesize, 
            contenthash,
            filearea, 
            from_unixtime(timecreated,'%Y-%m-%d %H:%i:%s') as time 
            from mdl_files where filearea = 'draft' and userid=" . $fromform->useridf . ";  ";
        // $sql.=" ORDER BY tamanio DESC";

        $result = $DB->get_records_sql($sql);






        $espacio = 0;
        foreach ($result as $r) {
            $espacio += $r->filesize;
        }


        echo "<span><h3>" . get_string('ficherosencontrados', 'report_vivo') . ": " . count($result) . "</h3></span>";
        echo "<span><h3>" . get_string('espacioocupado', 'report_vivo') . ": " . $espacio . "&nbsp;bytes</h3></span>";

        echo "<a href='#'  class='btn btn-warning' onclick='eliminar()'>" . get_string('eliminartodos', 'report_vivo') . "</a> ";
        echo "<a href='#'  class='btn btn-warning' onclick='trashdir()'>" . "Limpiar Trash" . "</a> ";

        // echo '<div class="my-auto">' . $OUTPUT->lang_menu(true) . '</div>';
        // echo $OUTPUT->search_box();


        //$tablef->sortable(true,'tamanio',SORT_DESC);

        //$tablef-collapsible(false);

        $tablef->define_baseurl($url);
        $tablef->define_columns(array('fn', 'fs', 'hs', 'fa', 'tm', 'dl'));


        $tablef->define_headers(array("FileName", "Size", 'Hash', "Filearea", "Timecreated", "Borrar"));


        $tablef->setup();

        foreach ($result as $r) {

            $columnas = array();
            array_push($columnas, $r->filename);
            array_push($columnas, $r->filesize);
            array_push($columnas, $r->contenthash);
            array_push($columnas, $r->filearea);
            array_push($columnas, $r->time);

            $borrar = "<a onclick='verpermisos()' href='?del=" . $r->id . "' >" . get_string('borrar', 'report_vivo') . "</a>";
            array_push($columnas, $borrar);


            $tablef->add_data($columnas);
        }

    ?>
        <script>
            function verpermisos() {
                <?php if (!is_siteadmin()) { ?>
                    alert("Solo administradores pueden ejecutar esta accion.")
                <?php } ?>

            }

            function eliminar() {

                <?php if (is_siteadmin()) { ?>
                    r = confirm("Confirme que desea eliminar todos los archivos DRAFT de este usuario. Los archivos se moveran a Trash. Esta accion es irreversible.");
                    if (r) {
                        location.href = "/report/vivo/drafts.php?flush=true&iduf=<?php echo $fromform->useridf ?>"
                    }
                <?php } else { ?>
                    alert("Solo administradores pueden ejecutar esta accion.")
                <?php } ?>


            }
        </script>
<?php

        $tablef->set_control_variables(
            array(
                TABLE_VAR_SORT => 'ssort',
                TABLE_VAR_IFIRST => 'sifirst',
                TABLE_VAR_ILAST => 'silast',
                TABLE_VAR_PAGE => 'sPAGE',
            )
        );

        // $tablef->out(40,true);

        //$tablef->print_html();
        $tablef->finish_output();




        //echo "</div>";
        //}

        //}







    }
}




if (!$tablef->is_downloading()) {
    echo $OUTPUT->footer();
}
