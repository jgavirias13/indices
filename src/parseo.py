import argparse

#Recibir parametros por terminal
parser = argparse.ArgumentParser()
parser.add_argument('-f', '--file', action='store', required=True,
                    help='Nombre del archivo a parsear', dest='archivo')
parser.add_argument('-i', '--idioma', action='store', required=True,
                    help='Idioma del archivo', dest='idioma')
parser.add_argument('-n', '--numero', action='store', required=True, type=int,
                    help='Numero en el que inicia el iterador', dest='n')
argumentos = parser.parse_args()

#Abrir archivo que se desea parsear
archivo = open(argumentos.archivo)
n = argumentos.n
idioma = argumentos.idioma

#Recorrer cada linea del archivo
for linea in archivo:
  registro = linea.split('\t')
  word = registro[0][1:-1]
  info = registro[1][1:-2]
  info = info.split(',')
  count = info[0]
  doc = info[1][2:-1]
  cadena = str(n)+","+word+","+doc+","+count+","+idioma
  print cadena
  n += 1
