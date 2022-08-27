#!/usr/bin/python3

from imageio import imread
from numpy import packbits, concatenate
import sys

if len(sys.argv) < 3 :
	print("Usage: %s inFile outFile width height" % sys.argv[0]);
	sys.exit(-1);

imgData = imread(sys.argv[1], pilmode="RGB");
width = int(sys.argv[3])
height = int(sys.argv[4])
size = int(width * height / 8);

isRed = (imgData[:,:,0] > 0) & (imgData[:,:,1] == 0) & (imgData[:,:,2] == 0);
dataRed = packbits((~isRed).reshape((size, 8)));

isBlack = (imgData[:,:,:] > 0).sum(axis=2) == 0;
dataBlack = packbits((~isBlack).reshape((size, 8)));

dataAll = concatenate([dataBlack, dataRed]);

with open(sys.argv[2], "wb") as f : 
        f.write(bytearray(dataAll)); 
        f.close(); 

sys.exit(0);
