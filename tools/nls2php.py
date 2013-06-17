#!/usr/bin/python
# -*- encoding: utf-8 -*-
# by Sagol

import struct
import binascii
import sys
import os
from os.path import join, getsize

def Read (fileName):
  fd = open (fileName, 'rb')
  buf = fd.read ()
  fd.close ()
  return buf

def Write (fileName, buf):
  fd = open (fileName, 'wb+')
  fd.write (buf)
  fd.close ()

def Main (iFrom, iTo, iNum):
#  print "Main"
  fd = open (iFrom, 'rb')
  buf = []
  count = 0
  while 1 :
    count += 1
    s = fd.readline ()
    if len (s) == 0:
      break;
    if count < int (iNum) or s[0] == '#':
      continue
    s = s.rstrip ('\n')
    s = s.rstrip ('\r')
    m = s.split ('@')
    if len (m) < 2:
      print 'Error ! Номер строки : %d' % (count)
      continue
    r = "\t'%s' => '%s',\n" % (m[0], m[1])
    buf.append (r)
  fd.close ()
  fd = open (iTo, 'wb+')
  for i in buf:
    fd.write (i)
  fd.close ()

if __name__ == "__main__":
  if len (sys.argv) < 3 :
    print 'Мало аргументов'
    exit (0)
  pathFrom = str (sys.argv[1])
  pathTo = str (sys.argv[2])
  num = str (sys.argv[3])
  print pathFrom, pathTo
  Main (pathFrom, pathTo, num)

