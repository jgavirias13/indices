from mrjob.job import MRJob
from mrjob.step import MRStep
from pymongo import MongoClient
import os
import unicodedata
import string, re

mongoclient = MongoClient('localhost',27017)
db = mongoclient.jgaviri6
coleccion = db.palabras


def eliminarTildes(cadena):
    s = ''.join((c for c in unicodedata.normalize('NFD',unicode(cadena)) if unicodedata.category(c) != 'Mn'))
    return s

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
        yield word[0],(rep,word[1])

    def reducer2(self, word, doc):
        l = list(doc)
        documentos = sorted(l)
        if len(documentos) > 5:
            documentos = [l[0],l[1],l[2],l[3],l[4]]
        coleccion.insert({"palabra":word,"documentos":l})
        yield word,l
    
    def steps(self):
        return [
            MRStep(mapper=self.mapper,reducer=self.reducer),
            MRStep(reducer=self.reducer2)
        ]

if __name__ == '__main__':
    Contador.run()
