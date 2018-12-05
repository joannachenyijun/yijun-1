'''Given a knight in a chessboard (a binary matrix with 0 as empty and 1 as barrier) with a source position, find the shortest path to a destination position, return the length of the route.
Return -1 if knight can not reached.

Example
[[0,0,0],
 [0,0,0],
 [0,0,0]]
source = [2, 0] destination = [2, 2] return 2

[[0,1,0],
 [0,0,0],
 [0,0,0]]
source = [2, 0] destination = [2, 2] return 6

[[0,1,0],
 [0,0,1],
 [0,0,0]]
source = [2, 0] destination = [2, 2] return -1
Clarification
If the knight is at (x, y), he can get to the following positions in one step:

(x + 1, y + 2)
(x + 1, y - 2)
(x - 1, y + 2)
(x - 1, y - 2)
(x + 2, y + 1)
(x + 2, y - 1)
(x - 2, y + 1)
(x - 2, y - 1)
Notice
source and destination must be empty.
Knight can not enter the barrier.'''



class Point:

	def __init__(self, a=0, b=0):
		self.x = a
		self.y = b


class Solution:

	def shortestPath(self, grid, source, destination):

		row = len(grid)
		col = len(grid[0])

		count = 0
		step = [ ]
		new_source = []
		new_source.append(source.x)
		new_source.append(source.y)

		step.append(new_source)

		

		visit = [[False for x in range(row)] for y in range(col)]

		point = Point(x, y)

		#test
		#result = self.dfs(grid, point, row, col, visit)
		#return result
		visit[source.x][source.y] = True
		while step:

			tmp = step[0]
		
			
			step.pop(0)
			point.x = tmp[0]
			point.y = tmp[1]

			target = []
			target.append(destination.x)
			target.append(destination.y)

			step += self.dfs(grid, point, row, col, visit)
			
			
			
			count += 1
			
			
			if target in step:
				return count

		return -1


	def check(self, grid, new_point, row, col, visit):

		
		if new_point.x < row and new_point.x >= 0 and new_point.y >= 0 \
			and new_point.y < col and visit[new_point.x][new_point.y] == False \
			and grid[new_point.x][new_point.y] == 0 :
			

			return True

	def dfs(self, grid, point, row, col, visit):

		jump = [ ]

		xdir = [-2, -2, -1, -1, 1, 1, 2, 2]
		ydir = [-1, 1, -2, 2, -2, 2, -1, 1]
		for k in range(8):
			new_x = point.x + xdir[k]
			new_y = point.y + ydir[k]
			value = [ ]
			

			value.append(new_x)
			value.append(new_y)
			
			
			
			
			new_point = Point(new_x, new_y)

			if self.check(grid, new_point, row, col, visit):

				visit[new_point.x][new_point.y] = True
				jump.append(value)
				

		return jump





path = Solution()
source = Point(0, 0)
destination = Point(7, 0)
print(path.shortestPath([[0,0,0,0,1,1],[1,0,1,0,0,1],[0,0,1,0,0,1],[0,0,1,1,0,1],[1,0,1,0,0,1],[0,0,1,0,0,1],[0,0,1,0,0,1],[0,0,1,0,0,1]],source, destination))
