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

def Main (iFrom, lang):
  fd = open (iFrom, 'rb')
  buf = []
  buf_nls = []
  buf_state = []
  buf_php = []
  count = 0
  country = 0
  country_count = 0
  region = 0
  region_count = 0
  while 1 :
    count += 1
    string = fd.readline ()
    if len (string) == 0 :
      break;
    string = string.rstrip ('\n')
    string = string.rstrip ('\r')
    m = string.split (',')
    if len (m) < 3:
      print 'Error ! Номер строки : %d' % (count)
      continue
    if m[0] != country:
      Write ('%s.%s' % (country, lang) , buf)
      Write ('%s_%s.nls' % (country, lang) , buf_nls)
      Write ('%s_state_%s.nls' % (country, lang) , buf_state)
      Write ('%s_%s.php' % (country, lang) , buf_php)
      country = m[0]
      country_count = 0
      region_count = 0
      buf = []

    if m[1] != region:
      region = m[1]
      region_count = 0
    country_count += 1
    region_count += 1
    result       = 0
    result_nls   = 0
    result_state = 0
    result_php   = 0
    if region_count == 1:
      result = "\nNLS_%s\t\t%s\n\t\t\tNLS_%s\t\t%s\n" % \
        (region.replace(' ', '_').replace('-', '_').replace("'", '_').upper(), region, \
        m[2].replace(' ', '_').replace('-', '_').replace("'", '_').upper(), m[2])
      result_state = "NLS_%s\t%s\n" % \
        (region.replace(' ', '_').replace('-', '_').replace("'", '_').upper(), region)
      result_nls = "NLS_%s@%s\n" % \
        (region.replace(' ', '_').replace('-', '_').replace("'", '_').upper(), region)
      result_php = "\t'NLS_%s' => '%s',\n" % \
        (region.replace(' ', '_').replace('-', '_').replace("'", '_').upper(), region)
      buf_state.append (result_state)
    else:
      if len (m[2]) > 1:
        result = "\t\t\tNLS_%s\t\t%s\n" % (m[2].replace(' ', '_').replace("'", '_').replace('-', '_').upper(), m[2])
        result_nls = "NLS_%s@%s\n" % \
          (m[2].replace(' ', '_').replace("'", '_').replace('-', '_').upper(), m[2])
        result_php = "\t'NLS_%s' => '%s',\n" % \
          (m[2].replace(' ', '_').replace("'", '_').replace('-', '_').upper(), m[2])
    buf.append (result)
    buf_nls.append (result_nls)
    buf_php.append (result_php)
  fd.close ()
  Write ('%s.%s' % (country, lang) , buf)
  Write ('%s_%s.nls' % (country, lang) , buf_nls)
  Write ('%s_state_%s.nls' % (country, lang) , buf_state)
  Write ('%s_%s.php' % (country, lang) , buf_php)

if __name__ == "__main__":
  if len (sys.argv) < 2 :
    print 'Мало аргументов'
    exit (0)
  pathFrom = str (sys.argv[1])
  lang = str (sys.argv[2])
  print pathFrom
  Main (pathFrom, lang)
