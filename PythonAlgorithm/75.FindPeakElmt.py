'''There is an integer array which has the following features:

The numbers in adjacent positions are different.
A[0] < A[1] && A[A.length - 2] > A[A.length - 1].
We define a position P is a peak if:

A[P] > A[P-1] && A[P] > A[P+1]
Find a peak element in this array. Return the index of the peak.

Example
Given [1, 2, 1, 3, 4, 5, 7, 6]

Return index 1 (which is number 2) or 6 (which is number 7)

Challenge
Time complexity O(logN)'''


class solution:
	#A: integer array
	#return: index of any peak positions
	def findPeak(self,A):

		if not A:
			return -1

		start = 0
		end = len(A) - 1

		while start + 1 < end:
			mid = start + (end - start) // 2
			if A[mid] < A[mid - 1] and A[mid] > A[mid + 1]:
				end = mid
			else:
				start = mid

		if A[start] > A[start - 1] and A[start] > A[start + 1]:
		 	return start
		elif A[end] > A[end - 1] and A[end] > A[end + 1]:
		 	return end
		else:
		 	return mid 






x= solution()
print(x.findPeak([1,10,9,8,7,6,5,4]))
