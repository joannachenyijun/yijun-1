'''Given a big sorted array with positive integers sorted by ascending order. The array is so big so that you can not get the length of the whole array directly, and you can only access the kth number by ArrayReader.get(k) (or ArrayReader->get(k) for C++). Find the first index of a target number. Your algorithm should be in O(log k), where k is the first index of the target number.

Return -1, if the number doesn't exist in the array.

Example
Given [1, 3, 6, 9, 21, ...], and target = 3, return 1.

Given [1, 3, 6, 9, 21, ...], and target = 4, return -1.

Challenge
O(log k), k is the first index of the given target number.'''
class ArrayReader:
	def get(self,index):


class solution:
	#input is a big sorted ascending order array
	#output is integer index of target first appearance
	def SeachBigArray(self,reader,target):
		index = 0
		while reader.get(index) < target:
			index = 2* index + 1

		
		start, end = 0, index

		while start + 1 < end:
			mid = (start + end) // 2
			if reader.get(mid) >= target:
				end = mid
			else:
				start = mid

		if reader.get(start) == target:
			return start
		elif reader.get(end) == target:
			return end
		else:
			return -1
			