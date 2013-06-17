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
  fd = open (iFrom, 'rb')
  buf_nls = []
  buf_nls_list = []
  buf_php = []
  count = 0
  while 1 :
    count += 1
    s = fd.readline ()
    if len (s) == 0:
      break;
    if count < int (iNum) or s[0] == '#':
      continue
    l = s.split()
    i = 0
    st = '' 
    for x in l:
      if i == 0:
        st += 'NLS_'+ x.upper() + '@'
        buf_nls_list.append ('NLS_'+ x.upper() + '\n')
      else:
        if (i < len (l) - 1):
          st += x + ' '
        else:
          st += x 
      i = i + 1
    buf_nls.append (st + '\n')

    nls_str = st.split('@')
    if len (nls_str) >= 2:
      buf_php.append ("\t'%s' => '%s',\n" % (nls_str[0], nls_str[1])) 

  fd.close ()
  fd = open (iTo + '.nls', 'wb+')
  for i in buf_nls:
    fd.write (i)
  fd.close ()
  fd = open (iTo + '.nlsl', 'wb+')
  for i in buf_nls_list:
    fd.write (i)
  fd.close ()
  fd = open (iTo + '.php', 'wb+')
  for i in buf_php:
    fd.write (i)
  fd.close ()


if __name__ == "__main__":
  if len (sys.argv) < 3 :
    print 'Help:\n\tlist2php.py [input file] [output files name without extension] [init string counter]'
    exit (0)
  pathFrom = str (sys.argv[1])
  pathTo = str (sys.argv[2])
  num = str (sys.argv[3])
  print pathFrom, pathTo
  Main (pathFrom, pathTo, num)

