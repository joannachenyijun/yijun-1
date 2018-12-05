
'''Implement pow(x, n).

Example
Pow(2.1, 3) = 9.261
Pow(0, 1) = 0
Pow(1, 0) = 1
Challenge
O(logn) time'''


class solution:
	#input integerx as base
	#input integer n as power
	def myPower(self,x,n):

		if n >= 0 :
			flag = 1
		else:
			flag = -1

		result = 0
		cnt = 0
		n = abs(n)
        

        #overflow!!!

		while cnt <= n and result < 2 ** 32:
			if cnt % 2 == 1:
				result = result * x
			else:
				# reduce time comp to logn
				result = (x * x) ** (cnt / 2)

			cnt += 1

		if flag == 1:
			return result
		else:
			return 1 / result



x=solution()
print(x.myPower(2,-2147483648))


