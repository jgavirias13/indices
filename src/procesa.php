<html>
<head>
  <title>Busqueda</title>
</head>
<body>
    <form method="GET" action="procesa.php">
        <input type="text" name="palabra" />
        <input type="submit" value="Buscar"/>
    </form>

<?php
  $palabras = $_GET["palabra"];  
  function eliminar_tildes($cadena){
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    return $texto;
  }
  
  function sacar_documentos($result, $idioma){
    if($idioma == "es"){
      echo "<h3>Español:</h3>";
    }else if($idioma == "en"){
      echo "<h3>Ingles:</h3>";
    }
    echo "<p>";
    if(mysqli_num_rows($result) > 0) {
      $documentos = array();
      $palabras = array();
      while($row = mysqli_fetch_assoc($result)){
        if(array_key_exists($row["document"],$documentos)){
          $documentos[$row["document"]] += 100000 + (int)$row["cantidad"];
          $palabras[$row["document"]] .= $row["word"].", ";
        }else{
          $documentos[$row["document"]] = 100000 + (int)$row["cantidad"];
          $palabras[$row["document"]] = $row["word"].", ";
        }
      }
      if(arsort($documentos)){
        $i=0;
	echo "<ul>";
        foreach($documentos as $key => $val){
          if($i<5){
    	    $link = "<a href=\"http://10.131.137.188/".$idioma."/".$key."\">".$key."</a>";
            $apariciones = (int)($val/100000);
            $cantidad = (int)$val-$apariciones*100000;
            echo "<li><b>Documento:</b> ".$link.", <b>Palabras:</b> ".$palabras[$key]."<b> Ocurrencia:</b> ".$cantidad."</li>";
            $i++;
          }
        }
	echo "</ul>";
      }else{
        echo "Error al ordenar";
      }
    }else{
      echo "0 Resultados";
    }
    echo "</p>";
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

  function separar_palabras($cadena){
    $texto = explode(" ",$cadena);
    return $texto;
  }
  
  $palabra = eliminar_tildes($palabras);
  $palabraArray = separar_palabras($palabra); 
  $querystring = "SELECT word, document,cantidad,idioma FROM st0263.jgaviridgomez WHERE ("; 
  $tamano = count($palabraArray);
  for($i=0;$i<$tamano;$i++){
    $querystring .= " word = \"".$palabraArray[$i]."\"";
    if($i<$tamano-1){
      $querystring .= " or";
    }
  }
  $querystring1 = $querystring.") and idioma = \"es\"";
  $result = mysqli_query($conn, $querystring1);
  sacar_documentos($result,"es");
  $querystring2 = $querystring.") and idioma = \"en\"";
  $result = mysqli_query($conn, $querystring2);
  sacar_documentos($result,"en");
  mysqli_close($conn);
?>
</body>
</html>
