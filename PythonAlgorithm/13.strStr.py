
'''
For a given source string and a target string, you should output the first index(from 0) of target string in source string.

If target does not exist in source, just return -1.

Example
If source = "source" and target = "target", return -1.

If source = "abcdabcdefg" and target = "bcd", return 1.

Challenge
O(n2) is acceptable. Can you implement an O(n) algorithm? 
'''




class solution(object):
	#input a string
	#return integer index
	def strStr(self,source,target):
		# convert string into list , user function enumerate
		if target == "" and source != "":
			return -1

		if target != "" and source == "":
			return -1
		if target == "" and source == "":
			return 0

		if not source or not target:
			return -1

		if target in source:
			return source.find(target)
		else:
			return -1


x=solution()
print(x.strStr("null",""))