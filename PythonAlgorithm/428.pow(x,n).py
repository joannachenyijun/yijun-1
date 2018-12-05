class solution:
	#input integerx as base
	#input integer n as power
	def myPower(self,x,n):

		if n >= 0:
			flag = 1
		else:
			flag = -1

		result = 0
		n = abs(n)
		cnt = 0

		while cnt <= n:
			if cnt % 2 == 1:
				result = result * x
			else:
				x = x * x
				result = x**(cnt / 2)

			cnt += 1

		if flag = 1:
			return result
		else:
			return 1/result
x=solution()
print(x.myPower(2,3))


