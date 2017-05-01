from mrjob.job import MRJob
from mrjob.step import MRStep
import os
import string, re

def eliminarTildes(cadena):
    s = ''.join((c for c in unicodedata.normalize('NFD',unicode(cadena)) if unicodedata.category(c) != 'Mn'))
    return s.decode()

class Contador(MRJob):
    def mapper(self,name,line):
        for w in line.decode('utf-8','ignore').split():
            filepath = os.environ["map_input_file"]
            filename = os.path.split(filepath)[-1]
            sinSignos = re.sub('[%s]' % re.escape(string.punctuation),'',w)
            if sinSignos:
                sinTildes = eliminarTildes(sinSignos)
                yield (sinTildes,filename),1
    
    def reducer(self, word, cant):
        rep = sum(cant)
        l = list(word)
        yield word[0],(word[1],rep)

if __name__ == '__main__':
    Contador.run()
