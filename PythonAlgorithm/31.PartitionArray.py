'''Given an array nums of integers and an int k, partition the array (i.e move the elements in "nums") such that:

All elements < k are moved to the left
All elements >= k are moved to the right
Return the partitioning index, i.e the first index i nums[i] >= k.

Example
If nums = [3,2,2,1] and k=2, a valid answer is 1.

Challenge
Can you partition the array in-place and in O(n)?'''




class solution:
	#
	#
	#
	def partitionArray(self, nums, k):

		for i in range (0, len(nums)):
			if nums[i] == k:
				location = i
				break


		start = 0
		end = len(nums) - 1
		
	
		

		while start <= end:
			print(nums)
			while nums[start] < k and start <= end:
				start += 1
				

			while nums[end] >= k and start <= end:
				end -= 1
				

			if start <= end:
				nums[start], nums[end] = nums[end], nums[start]
				
		
				start += 1
				end -= 1	
		return start

x = solution()
print(x.partitionArray([110,159,48,56,24,192,126,109,102,103,183,194,110,155,110],110))