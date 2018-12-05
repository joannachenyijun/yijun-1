
'''Example
In array [9,3,2,4,8], the 3rd largest element is 4.

In array [1,2,3,4,5], the 1st largest element is 5, 2nd largest element is 4, 3rd largest element is 3 and etc.

Challenge
O(n) time, O(1) extra memory.
'''

class solution:
    # @param k & A a integer and an array
    # @return ans a integer
    def partition(self, nums, k):
    	pivot = nums[0]
    	nums1 = []
    	nums2 = []
    	for num in nums:
    		if num > pivot:
    			nums1.append(num)
    		elif num < pivot:
    			nums2.append(num)

    	if k <= len(nums1):
    		return self.partition(nums1, k)
    	if k > len(nums) - len(nums2):
    		return self.partition(nums2, k - (len(nums) - len(nums2)))

    	return pivot


x = solution()
print(x.partition([9, 3, 2, 4, 8], 3))