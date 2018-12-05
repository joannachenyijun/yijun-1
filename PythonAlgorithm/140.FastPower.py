'''Calculate the an % b where a, b and n are all 32bit positive integers.

Example
For 231 % 3 = 2

For 1001000 % 1000 = 0

Challenge
O(logn)
'''



class solution:
	#input a
	#input b
	#input n
	#return integer
	def fastPower(self, a, b, n):

		if n == 0:
			result = 1 % b
			return result



		result = 1
		while n > 0:
			# if power is odd
			if n % 2 == 1:
				result = (result * a) % b

			n = n // 2
			a = (a * a) % b

			

		return result 


x = solution()
print(x.fastPower(31,1,0))





