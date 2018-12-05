'''Suppose a sorted array is rotated at some pivot unknown to you beforehand.

(i.e., 0 1 2 4 5 6 7 might become 4 5 6 7 0 1 2).

You are given a target value to search. If found in the array return its index, otherwise return -1.

You may assume no duplicate exists in the array.

Example
For [4, 5, 1, 2, 3] and target=1, return 2.

For [4, 5, 1, 2, 3] and target=0, return -1.

Challenge
O(logN) time
'''


class solution:
	#input array
	#input target
	#return index
	def search(self, nums, target):

		if not nums or target is None:
			return -1


		start = 0
		end = len(nums) - 1
		if len(nums) == 0:
			return -1


		while start + 1 < end:
			mid = start + (end - start) // 2
			if nums[mid] == target:
				return mid
			if nums[start] < nums[mid]:
				if nums[start] <= target and nums[mid] > target:
					end = mid
				else:
					start = mid

			else:
				if nums[end] >= target and nums[mid] < target:
					start = mid
				else:
					end = mid
			





		if nums[start] == target:
			return start
		if nums[end] == target:
			return end
		
		return -1
		
x = solution()
print(x.search([0,1,2,-10,-9,-8,-7,-6,-5,-4,-3,-2,-1],-9))