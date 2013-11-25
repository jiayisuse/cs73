#!/usr/bin/env python

import sys
import re
import math
import os
import glob
import nltk
import json
import include

categories_info = []

lambdaa = 0.2 

html = sys.argv[1].lower()

def do_read_train(uni_dict, bi_dict, train_file):
	file = open(train_file, "r")
	lines = file.readlines()
	file.close()
	for line in lines:
		words = line.split()
		'''
		if words[4] == "3":
			break
		'''
		bi_dict[words[0]] = [words[1], words[2]]
		uni_dict[words[0].split("|")[1]] = [words[3], words[4]]
	num_tokens = line.split()[-1]
	uni_dict["UNK"] = [min(include.min_UNK, 1 / (float(num_tokens))), "1"]


def read_train_info(categories_info, dataset_dir):
	def read_train(categories_info, dirpath, namelist):
		pattern = os.path.join(dirpath, "*" + include.bi_train_suffix)
		for bi_train_file in glob.glob(pattern):
			categories = bi_train_file[len(dataset_dir):]
			categories = os.path.split(categories)[0]
			category_names = categories.split("/")
			#print category_names
			uni_train_dict = {}
			bi_train_dict = {}
			do_read_train(uni_train_dict, bi_train_dict, bi_train_file)
			category_info = []
			category_info.append(category_names[1])
			category_info.append(uni_train_dict)
			category_info.append(bi_train_dict)
			categories_info.append(category_info)
	os.path.walk(dataset_dir, read_train, categories_info)

def suprisal(first, second, uni_dict, bi_dict):
	if uni_dict.has_key(first):
		uni_p = float(uni_dict[first][0])
	else:
		uni_p = float(uni_dict["UNK"][0])

	key = first + "|" + second
	if bi_dict.has_key(key):
		bi_p = float(bi_dict[key][0])
	else:
		bi_p = 0
	p = (1 - lambdaa) * bi_p + lambdaa * uni_p
	return -math.log(p, 2)

read_train_info(categories_info, include.dataset_dir)
text = nltk.clean_html(html)
tokens = include.my_tokenizer(text)
num_tokens = len(tokens)

for cate_info in categories_info:
	uni_train_dict = cate_info[1]
	bi_train_dict = cate_info[2]
	entropy = 0.0
	for i in range(1, num_tokens):
		first = tokens[i]
		second = tokens[i - 1]
		entropy += suprisal(first, second, uni_train_dict, bi_train_dict)
	entropy /= num_tokens
	ppl = 2 ** entropy
	cate_info.append(ppl)

sorted_cate = sorted(categories_info, key = lambda x: x[3])
min_category = sorted_cate[0]

cate_names = []
for i in range(min(3, len(sorted_cate))):
	if min_category[3] <= include.ppl_distinct and sorted_cate[i][3] > include.ppl_max:
		continue
	cate_names.append(sorted_cate[i][0])
	#print sorted_cate[i][0], " ", sorted_cate[i][3]

if min_category[3] > include.ppl_distinct:
	cate_names.insert(0, "0")

print json.dumps(cate_names)
