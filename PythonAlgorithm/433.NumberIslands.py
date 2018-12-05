'''Given a boolean 2D matrix, 0 is represented as the sea, 1 is represented as the island. If two 1 is adjacent, we consider them in the same island. We only consider up/down/left/right adjacent.

Find the number of islands.

'''


class solution:
	#input matrix
	#return integer

	def numIslands(self, grid):

		row = len(grid)
		col = len(grid[0])

		if row == 0:
		 	return 0

		count = 0

		for x in range (0, row):
			for y in range (0, col):
				if grid[x][y] == 1:
					self.bfs(grid, x, y)
					count += 1
		return count

	def bfs(self, grid, x, y):
			#direction
			xdir = [-1, 0, 1, 0]
			ydir = [0, -1, 0, 1]
			#initialization a queue
			queue = [(x, y)]

			while len(queue) > 0 :
				x = queue[0][0]
				y = queue[0][1]
				queue.pop(0)

				for k in range(0, 4):
					newx = x + xdir[k]
					newy = y + ydir[k]
					if grid[x][y] == 1:
						grid[x][y] == 0
						queue.append((newx, newy))

	



x = solution()
print(x.numIslands([[1,1,0,0,0],[0,1,0,0,1],[0,0,0,1,1],[0,0,0,0,0],[0,0,0,0,1]]))

			


