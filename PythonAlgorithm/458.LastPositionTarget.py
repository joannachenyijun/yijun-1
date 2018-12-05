'''Find the last position of a target number in a sorted array. Return -1 if target does not exist.

'''


class solution:
	#input is array list of integer, and target number
	#output integer index
	def LastPosition(self,nums,target):
		start = 0
		end = len(nums)-1
		
		if not nums or target is None:
			return -1

		while start + 1 < end:
			mid = start + (end - start)//2
			if target < nums[mid]:
				end = mid
				
			else:
				start = mid

		if nums[end] == target:
			return end
		elif nums[start] == target:
			return start
		else:
			return -1
			

x=solution()
print(x.LastPosition([0,0,0,0,0,1,1,1],0))			

