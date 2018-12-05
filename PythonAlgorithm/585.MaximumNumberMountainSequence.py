'''
Given a mountain sequence of n integers which increase firstly and then decrease, find the mountain top.

Example
Given nums = [1, 2, 4, 8, 6, 3] return 8
Given nums = [10, 9, 8, 7], return 10'''
class solution:
	#input list of integer
	# in integer as return
	def MaxNumMounSeq(self,nums):
		start = 0
		end = len(nums)-1
		if not nums:
			return -1
		if nums[0] > nums[1]:
			return nums[0]
		if nums[end-1] < nums[end]:
			return nums[end]
		if len(nums) == 1:
			return nums[0]


		while start + 1 < end:
			mid = start + (end - start)//2
			if nums[mid] > nums[mid+1] and nums[mid] < nums[mid-1]:
				end = mid
			elif nums[mid] < nums[mid+1] and nums[mid] > nums[mid-1]:
				start = mid 
			else:
				return nums[mid]
		
	

x=solution()
print(x.MaxNumMounSeq([16,15,14,13,5,4,2,1]))