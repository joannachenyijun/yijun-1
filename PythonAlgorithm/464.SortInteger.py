'''Given an integer array, sort it in ascending order. Use quick sort, merge sort, heap sort or any O(nlogn) algorithm.

'''



class solution:
	#input array
	#return array
	def sortIntegers2(self,A):
		self.quick(A, 0, len(A) - 1)

	def quick(self, A, start, end):
		if start >= end:
			return

		left, right = start, end
		pivot = A[(start + end) // 2]

		while left <= right:
			while left <= right and A[left] < pivot:
				left += 1

			while left <= right and A[right] > pivot:
				right -= 1

			if left <= right:
				A[left], A[right] = A[right], A[left]

				left += 1
				right -= 1	

		self.quick(A,start, right)
		self.quick(A,left,end)




x = solution()
print(x.sortIntegers2([1,3,3,4,2,1,3,4,5,6,7,8,6,5,4,2]))