# Indice Invertido

## Aplicación
La aplicación desplegada se encuentra en la dirección 10.131.137.150

## Autores
Juan Pablo Gaviria
David Alejandro Gomez

*Universidad Eafit*

## Indice
1. Diseño del sistema

	1.1. Herramientas usadas

	1.2. Algoritmos usados

2. Proceso ETL

	2.1. Extracción de datos

	2.2. Transformación de datos

	2.3. Carga de datos

## Diseño del sistema
![Diagrama del sistema](http://fotos.subefotos.com/7035e589f4d8d2e5636e07aad427bbe9o.png "Diagrama del sistema")

  El sistema se diseño en base a lo visto en clase. Los documentos de Gutenberg estan almacenados en el servidor 10.131.137.188, desde ahí son cargados en el sistema de archivos del cluster Hadoop para ser analizados a traves de un programa en Map Reduce. La salida producida esta lista para ser exportada por Sqoop en la base de datos MySQL almacenada en el servidor 10.131.137.188. Una vez los datos estan en la base de datos pueden ser accedibles desde el AppServer en el 10.131.137.150. Desde allí una aplicación en PHP consulta los registros de la base de datos en base a las ordenes del usuario. Estos procesos se ampliaran en la sección "Proceso ETL".

### Herramientas usadas
* HDFS: El sistema de ficheros de Hadoop se utilizó para almacenar los documentos Gutenberg antes de realizar el procesamiento sobre ellos. La justificación es que para ejecutar tareas de Map Reduce, los datos deben estar almacenados en el Cluster Hadoop y no en un sitio externo.
* Map Reduce: Las tecnicas Map Reduce se usaron para el procesamiento de los documentos y la realización del indice invertido. Se utiliza esta tecnica ya que permite ejecutar sobre el cluster Hadoop trabajos muy pesados que se resuelven en un periodo relativamente corto. Por la cantidad de documentos y de palabras, se decide utilizar estas tecnicas.
* Sqoop: Sqoop se usa para realizar la exportación de los datos a MySQL. Nos sirve para que la salida que esta almacenada en el cluster Hadoop, sea de una vez cargada en la base de datos.

### Algoritmos usados
Para cumplir con el objetivo de realizar una busqueda de palabras sobre una serie de documentos, se utilizaron los siguientes algoritmos:
* Limpieza de la cadena: Se uso una serie de tecnicas para eliminar los caracteres especiales, las tildes, signos de puntuación, etc. Esto con el fin de que las palabras que sean iguales pero estan escritas de una forma diferente (formato) puedan coincidir.
* Indice invertido: El algortimo que se usa para realizar la busqueda de palabras es el indice invertido. Este algoritmo relaciona cada palabra encontrado en los documentos con los documentos que las contienen. Como una variante agregamos ademas el número de veces que aparece la palabra en el documento. El indice invertido es el nucleo del algoritmo de Map Reduce realizado.

## Proceso ETL

### Extracción de datos
Como se explico en la sección diseño del sistema, los documentos se encuentran en el servidor 10.131.137.188 y se cargaron en el sistema de archivos HDFS de Hadoop

`hadoop fs -put /var/www/gutenberg/es/* /user/st0263/jgaviri6/data_in`

Con la instrucción anterior se subian todos los documentos en español a hadoop, lo mismo se hizo con los documentos en ingles.

### Transformación de datos
Una ves los datos estan en hadoop se procede a ejecutar el programa en Python con Map Reduce que se encarga de realizar el indice invertido. Este programa esta compuesto por 1 mapper y 2 reducer. Como salida genera un listado de registros, cada uno con una palabra, un documento donde aparece y la cantidad de ocurrencias que tiene en ese documento. Este listado sirve para que, despues de un cambio de formato, pueda exportarse a la base de datos.
Ademas de esto, el programa contiene algoritmos que se encargan de la limpieza de las cadenas, de tal forma que se cambien las mayusculas por minusculas, se quiten las tildes y se eliminen los caracteres especiales.
El comando que se utilizo para la ejecución del Map Reduce fue:

`python indice.py /var/www/gutenberg/es/*.txt -r hadoop --output-dir hdfs:///user/st0263/jgaviri6/salidaEs`

### Carga de datos
El proceso de carga consiste en preparar la salida del map reduce para exportar a MySQL y realizar el proceso de exportación. La salida del paso de transformación es formateada a traves del programa "parseo.py". Este programa se encarga de colocar cada registro en una linea y separar cada columna con una coma. Luego de tener en buen formato la salida, utilizamos Sqoop para exportar el archivo a MySQL.

`python parseo.py -f archivoParser -n 0 -i es`

`sqoop export --connect jdbc:mysql://10.131.137.188:3306/st0263 --username st0263 -P --table jgaviridgomez --export-dir /user/st0263/jgaviri6/out_dir`

La tabla de MySQL almacena cada registro con los valores de id, palabra, documento, cantidad de apariciones y idioma en el que esta el documento.

### Visualización de datos

Para el despliegue de la aplicación utilizamos el servidor 10.131.135.150. En este se aloja una aplicación en PHP que, en base a las palabras que busca el usuario, realiza una consulta en la base de datos para obtener los documentos. Esta aplicación tambien realiza un proceso de limpieza a las palabras ingresadas por el usuario.