'''

Check whether the original sequence org can be uniquely reconstructed from the sequences in seqs. The org sequence is a permutation of the integers from 1 to n, with 1 ≤ n ≤ 10^4. Reconstruction means building a shortest common supersequence of the sequences in seqs (i.e., a shortest sequence so that all sequences in seqs are subsequences of it). Determine whether there is only one sequence that can be reconstructed from seqs and it is the org sequence.

Example
Given org = [1,2,3], seqs = [[1,2],[1,3]]
Return false
Explanation:
[1,2,3] is not the only one sequence that can be reconstructed, because [1,3,2] is also a valid sequence that can be reconstructed.

Given org = [1,2,3], seqs = [[1,2]]
Return false
Explanation:
The reconstructed sequence can only be [1,2].

Given org = [1,2,3], seqs = [[1,2],[1,3],[2,3]]
Return true
Explanation:
The sequences [1,2], [1,3], and [2,3] can uniquely reconstruct the original sequence [1,2,3].

Given org = [4,1,5,2,6,3], seqs = [[5,2,6,3],[4,1,5,2]]
Return true'''



class Solution:
	#param: org list
	#param: seqs, list of list
	def sequenceReconstruction(self, org, seqs):

		indexes = {e:i for i, e in enumerate(org)}
		print(indexes)
		edges = set()

		if not seqs:
			return False

		for seq in seqs:
			for s in seq:
				if s not in indexes:
					return False
			for i in range(1, len(seq)):
				pre, cur = seq[i - 1], seq[i]
				if indexes[pre] > indexes[cur]:
					return False
				edges.add((pre, cur))
		print(edges)

		for x in range(1, len(org)):
			if (org[x - 1], org[x]) not in edges:
				return False
		return True



x = Solution()
print(x.sequenceReconstruction([1, 2, 3],[[1, 2],[1, 3],[2, 3]]))