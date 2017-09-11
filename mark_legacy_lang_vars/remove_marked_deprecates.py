__author__ = 'xus'
import fileinput
import sys
import os.path

#ilias.en.lang as an argument
if len(sys.argv) is not 2:
    sys.exit("Error: Invalid number of arguments, full path for ilias_en.lang is required.")

english_file = sys.argv[1]

if not os.path.isfile(english_file):
    sys.exit("Error: English language file not found => " + english_file)

list_deprecateds = list()

with open(english_file) as r:
    for line in r:
        if "###deprecated" not in line:
            list_deprecateds.append(line)

f = open(english_file, 'w')
f.writelines(list_deprecateds)
f.close()