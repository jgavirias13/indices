<html>
<head>
  <title>Busqueda</title>
</head>
<body>
<?php
    
  function eliminar_tildes($cadena){
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    return $texto;
  }
  
  //Crear conexion de mysql
  $servername = "10.131.137.188";
  $username = "st0263";
  $password = "st0263.2017";
  $dbname = "st0263";
  
  $conn = new mysqli($servername, $username, $password, $dbname, 3306);
  if($conn -> connect_error){
    die("Conexion a base de datos ha fallado: " . $conn -> connect_error);
  }
  
  $palabras = $_GET["palabra"];
  $palabra = eliminar_tildes($palabras);
  echo "$palabra";

?>
</body>
</html>
