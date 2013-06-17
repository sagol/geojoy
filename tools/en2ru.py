#!/usr/bin/python
# -*- encoding: utf-8 -*-

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
  if len(buf) == 0:
    return
  fd = open (fileName, 'wb+')
  for i in buf:
    fd.write (str(i))
  fd.close ()

def nls2php (iFrom):
  fd = open (iFrom, 'rb')
  buf = []
  count = 0
  while 1 :
    s = fd.readline ()
    if len (s) == 0 :
      break;

    s = s.rstrip ('\n')
    s = s.rstrip ('\r')
    m = s.split ('@')
    if len (m) < 2:
      print 'Error ! Íîìåð ñòðîêè : %d' % (count)
      continue
    r = "\t'%s' => '%s',\n" % (m[0], m[1])
    buf.append (r)
  fd.close ()
  Write (iFrom.replace ('en','ru').replace ('nls','php'), buf)

def Main (iFrom):
  fd = open (iFrom, 'rb')
  buf = []

  while 1 :
    string = fd.readline ()

    if len (string) == 0 :
      break;

    result = string.replace (' ','@', 1)
    buf.append (result)

  fd.close ()
  out = iFrom.replace ('en','ru')
  Write (out, buf)
  nls2php (out)

if __name__ == "__main__":
  if len (sys.argv) < 1 :
    print 'ÐœÐ°Ð»Ð¾ Ð°Ñ€Ð³ÑƒÐ¼ÐµÐ½Ñ‚Ð¾Ð²'
    exit (0)
  pathFrom = str (sys.argv[1])
  print pathFrom
  Main (pathFrom)
