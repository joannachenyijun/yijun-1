'''Suppose a sorted array is rotated at some pivot unknown to you beforehand.

(i.e., 0 1 2 4 5 6 7 might become 4 5 6 7 0 1 2).

Find the minimum element.

Example
Given [4, 5, 6, 7, 0, 1, 2] return 0

Notice
You may assume no duplicate exists in the array.'''





class solution:
	#input array
	#ouput interger index
	def findMin(self,nums):

		start = 0
		end = len(nums)-1

		if not nums:
			return -1

		while start + 1 < end:
			mid = start + (end - start) // 2
			if abs(nums[mid] - nums[end]) > abs(nums[mid] - nums[start]):
				start = mid
			else:
				end = mid


		if nums[start] < nums[start + 1] and nums[start] < nums[start - 1]:
			return nums[start]
		elif end == len(nums) - 1 and nums[end] < nums[end - 1]:
			return nums[end]
		elif nums[end] < nums[end + 1] and nums[end] < nums[end - 1]:
			return nums[end]
		else:
			return nums[mid]

x = solution()
print(x.findMin([2,1]))