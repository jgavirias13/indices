# Indice Invertido

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
