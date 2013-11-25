import re
import nltk

dataset_dir = "./data"
uni_train_suffix = ".uni_train"
bi_train_suffix = ".bi_train"
test_html = "kernel.html"

min_UNK = 0.0001
ppl_distinct = 1000
ppl_max = 2000

def my_tokenizer(text):
	text = text.lower()
	#tokens = re.split("\W+", text)
	tokens = nltk.word_tokenize(text)
	for i in reversed(range(len(tokens))):
		if tokens[i].isdigit() or re.match("\|+", tokens[i]) or len(tokens[i]) > 256:
			del tokens[i]
	return tokens
