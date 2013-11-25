#!/usr/bin/env python

import nltk
import os
import sys
import include

title = sys.argv[1].lower()
html = sys.argv[2].lower()
cate_id = sys.argv[3]

def do_read_train(uni_dict, bi_dict, file):
	lines = file.readlines()
	for line in lines:
		words = line.split()
		bi_dict[words[0]] = int(words[2])
		uni_dict[words[0].split("|")[1]] = int(words[4])
	return int(lines[0].split()[-1])

def frequency_update(uni_dict, bi_dict, new_uni_dict, new_bi_dict):
	# update uni dict
	for token in new_uni_dict:
		if uni_dict.has_key(token):
			uni_dict[token] += new_uni_dict[token]
		else:
			uni_dict[token] = new_uni_dict[token]

	# update bi dict
	for key in new_bi_dict:
		if bi_dict.has_key(key):
			bi_dict[key] += new_bi_dict[key]
		else:
			bi_dict[key] = new_bi_dict[key]

def sort_dict_to(uni_dict, bi_dict, n, sorted_list):
	for key in bi_dict:
		first = key.split("|")[0]
		second = key.split("|")[1]
		sorted_list.append([key, float(bi_dict[key]) / uni_dict[second], bi_dict[key], float(uni_dict[second]) / n, uni_dict[second], n])
	sorted_list = sorted(sorted_list, key = lambda x: x[4], reverse= True)
		
text = nltk.clean_html(html)
cate_dir = os.path.join(include.dataset_dir, cate_id)
if not os.access(cate_dir, os.F_OK):
	os.makedirs(cate_dir)
file = open(os.path.join(cate_dir, title + ".txt"), "w")
file.write(text)
file.close()

train_file = os.path.join(cate_dir, cate_id + include.bi_train_suffix)
uni_dict = {}
bi_dict = {}
n = 0
try:
	with open(train_file, "r") as file:
		n = do_read_train(uni_dict, bi_dict, file)
		file.close()
except IOError:
	pass

tokens = include.my_tokenizer(text)
if "" in tokens:
	tokens.remove("")

# read unigram frequency from new post
num_tokens = len(tokens)
new_uni_dict = {}
for token in tokens:
	if new_uni_dict.has_key(token):
		new_uni_dict[token] += 1
	else:
		new_uni_dict[token] = 1

# read bigram frequency from new post
new_bi_dict = {}
for i in range(1, len(tokens)):
	key = tokens[i] + "|" + tokens[i - 1]
	if new_bi_dict.has_key(key):
		new_bi_dict[key] += 1
	else:
		new_bi_dict[key] = 1

frequency_update(uni_dict, bi_dict, new_uni_dict, new_bi_dict)
sorted_list = []
sort_dict_to(uni_dict, bi_dict, n + num_tokens, sorted_list)
file = open(train_file, "w")
file.truncate()
for item in sorted_list:
	token = item[0]
	bi_p = item[1]
	bi_freq = item[2]
	uni_p = item[3]
	uni_freq = item[4]
	nn = item[5]
	file.write("%-30s %.8f %6d %16.8f %6s %9d\n" %(token, bi_p, bi_freq, uni_p, uni_freq, nn))
file.close()
