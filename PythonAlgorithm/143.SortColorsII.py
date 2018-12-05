
'''
Given an array of n objects with k different colors (numbered from 1 to k), sort them so that objects of the same color are adjacent, with the colors in the order 1, 2, ... k.

Example
Given colors=[3, 2, 2, 1, 4], k=4, your code should sort colors in-place to [1, 2, 2, 3, 4].

Challenge
A rather straight forward solution is a two-pass algorithm using counting sort. That will cost O(k) extra memory. Can you do it without using extra memory?

'''


class solution:
	#input array
	#return array, inplace sort
	def sortColors(self, color, k):

		self.sort_q(color, 0, len(color)-1)
		return color

	def sort_q(self, color, start, end):

		if start < end:
			p = self.split_point(color, start, end)
			self.sort_q(color, start, p)
			self.sort_q(color, p+1, end)


	def split_point(self, color, start, end):

		pivot = color[start]

		while True:
			while color[start] < pivot:
				start += 1

			while color[end] > pivot:
				end -= 1

			if start >= end:
				return end


			color[start], color[end] = color[end], color[start]
			start += 1
			end -= 1




x = solution()
print(x.sortColors([3,2,2,1,4],4))