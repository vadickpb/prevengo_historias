<?php 
$idatencion=$_GET['idatencion'];

require_once "../modelos/Persona.php";

$persona = new Persona();
$rspta=$persona->imprimirHistoria($idatencion);
$reg=$rspta->fetch_object();

$rspta2=$persona->imprimirReceta($idatencion);

//Obtenemos valores de la base de datos
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"]))
{
  header("Location: login.html");
}
else
{
if ($_SESSION['pacientes']==1)
{
 
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Receta</title>
	<link rel="stylesheet" href="../public/css/bootstrap.min.css">
</head>
<body>
  <table width="50%" border="1">
      <tr>
        <td colspan="3"><center><h3><b>CENTRO MEDICO M & B</b></h3></center></td>
      </tr>
      <tr>
        <td colspan="3"><center><h3><b>RECETA</b></h3></center></td>
      </tr>
      <tr>
        <td colspan="3"><center><img src="../public/img/prevengo.jpeg" width="300px" heigth="200px"></center></td>
      </tr>
      <tr>
        <td colspan="3"><h4><b>APELLIDOS Y NOMBRES: </b> <?php echo $reg->paciente; ?></h4></td>
      </tr>
      <tr>
        <td colspan="3"><h4><b>TRATAMIENTO: </b> <?php echo str_replace("\n", "<br>", $reg->tratamiento); ?></h4></td>
      </tr>
      <tr>
        <td colspan="3" style="background-color:#D8D8D8;"><center><h4>Detalle de Receta</h4></center></td>
      </tr>
      <tr>
        <td colspan="3">
          <table width="100%" border="1">
            <tr style="background-color:#D8D8D8;">
              <th>Medicamento</th>
              <th>Presentación</th>
              <th>Dosis</th>
              <th>Duración</th>
              <th>Cantidad</th>
            </tr>
            <?php 
              while ($reg2 = $rspta2->fetch_object()) {
            ?>
            <tr>
              <td>&nbsp;<?php echo $reg2->medicamento; ?></td>
              <td>&nbsp;<?php echo $reg2->presentacion; ?></td>
              <td>&nbsp;<?php echo $reg2->dosis; ?></td>
              <td>&nbsp;<?php echo $reg2->duracion; ?></td>
              <td>&nbsp;<?php echo $reg2->cantidad; ?></td>
            </tr>
            <?php 
              }
            ?>
          </table>
        </td>
      </tr>
      <tr>
        <td><h4>Fecha : <?php echo $reg->fecha;?></h4></td>
        <td><h4>hora : <?php echo $reg->hora;?></h4></td>
      </tr>
</table>
</body>
</html>
<?php
}
else
{
  require '../vistas/noacceso.php';
}
?>
<?php 
}
ob_end_flush();
?>