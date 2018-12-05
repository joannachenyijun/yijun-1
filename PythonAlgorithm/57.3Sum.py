'''
Given an array S of n integers, are there elements a, b, c in S such that a + b + c = 0? Find all unique triplets in the array which gives the sum of zero.

Example
For example, given array S = {-1 0 1 2 -1 -4}, A solution set is:

(-1, 0, 1)
(-1, -1, 2)
Notice
Elements in a triplet (a,b,c) must be in non-descending order. (ie, a ≤ b ≤ c)
'''

class solution:
	#input array
	#return list
	def threeSum(self, numbers):
		#remove zero first
		
		three_sum = [ ]
		sum_total = [ ]
		for i in range (0, len(numbers)):
			three_sum += self.twoSum(numbers, i)


		for point in three_sum:
			if point not in sum_total:
				sum_total.append(point)

		sum_total.sort(key=lambda x:x[::-1])

		return sum_total

	def twoSum(self, numbers, index):

		nums = [ ]
		target = numbers[index]
		for i in range (0, len(numbers)):
			if i != index:
				nums.append(numbers[i])

		two_sum = [ ]
		for m in range(0, len(nums) - 1):
			for n in range(m+1, len(nums)):
				if nums[n] == - target - nums[m]:
					pair = tuple(sorted([target, nums[m], nums[n]]))
					if pair not in two_sum:
						two_sum.append(pair)

		return two_sum




x = solution()
print(x.threeSum([-1,0,1,2,-1,-4]))



